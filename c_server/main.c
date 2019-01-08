#include <netdb.h> 
#include <unistd.h>
#include <stdio.h>
#include <netinet/in.h> 
#include <stdlib.h> 
#include <string.h> 
#include <sys/socket.h> 
#include <sys/types.h> 
#include <sys/wait.h>
#include <time.h>
#include <ctype.h>
#include <errno.h>
#include <libpq-fe.h>
#include "func_util.h"

#define MAX 2048 
#define TIMEOUT 100
#define SA struct sockaddr 

typedef struct {
	char* event_id;
	char* event_title;
	char* event_organizer;
	char* event_city;
	char* event_date;
	char* event_time;
	char* event_deadline;
	char* event_description;
	char* event_address;
	char* event_capacity;
	char* event_theme;
	char* event_guest;
	char* event_state;
} EVENT_T;


typedef struct {
	char* message_content;
	char* message_date;
	char* message_time;
	char* message_writer;
	char** message_seen;
	int message_num_seen;
} MESSAGE_T;

void send_message(int sockfd, char* mes) {
	if(write(sockfd, mes, strlen(mes)) != strlen(mes)) {
		printf("[%d] Could not send response\n", getpid());
		exit(0);
	}
	printf("[%d]Sent: %s\n", getpid(), mes);
}

ssize_t readLine(int fd, void *buffer, size_t n) {
    ssize_t numRead;                    /* # of bytes fetched by last read() */
    size_t totRead;                     /* Total bytes read so far */
    char *buf;
    char ch;

    if (n <= 0 || buffer == NULL) {
        errno = EINVAL;
        return -1;
    }

    buf = buffer;                       
	memset(buf, 0, MAX);
    totRead = 0;
    while(totRead < MAX -1) {
        numRead = read(fd, &ch, 1);
        if (numRead == -1) {
            if (errno == EINTR)         
                continue;
            else
                return -1;              

        } else if (numRead == 0) {     
            if (totRead == 0)           
                return 0;
            else                        
                break;

        } else {                        
			if(ch != '\n' && ch != '\r')
				if (totRead < n - 1) {     
					totRead++;
					*buf++ = ch;
				}
			if (ch == '\n' || ch == '\r')
				break;
        }
    }
    *buf = '\0';
    return totRead;
}


int get_message(int sockfd, char** buff) {
	fd_set set;
	struct timeval timeout;
	int rv;

	FD_ZERO(&set); 
	FD_SET(sockfd, &set); 
	
	timeout.tv_sec = 8*60*60;
	timeout.tv_usec = 0;

	rv = select(sockfd + 1, &set, NULL, NULL, &timeout);
	if(rv == -1) {
		printf("[%d]Select\n", getpid()); 
		return -1;
	} else if(rv == 0) {
		send_message(sockfd, "TIMEOUT_EXIT\n");
		printf("[%d]Timeout\n", getpid());
		return -1;
	} else {
		do {
			readLine(sockfd, *buff, MAX);
		} while(strlen(*buff) == 0);
	}
 	printf("[%d]Received: %s\n", getpid(), *buff);
 	return 0;
}

int is_valid_login(char* login, char* pass) {
	return 1;
}

int authentication_protocol(int sockfd, PGconn *db_conn, char **username) {
	char *buff = malloc(sizeof(char) * MAX);
	send_message(sockfd, "ACK_AUTH\n");
	
	if(get_message(sockfd, &buff) < 0)
		return -1;
	char delim[] = " ";
	buff[strlen(buff)] = '\0';
	char *login = strtok(buff, delim);
	char *pass = strtok(NULL, delim);
	if(test_login(db_conn, login, pass) == true) {
		send_message(sockfd, "AUTH_ACCEPT\n");
		strcpy(*username, login);
		if(get_message(sockfd, &buff) < 0)
			return -1;
		if(strstr(buff, "ACK_ACCEPT")) {
			return 1;
		}
	} else {
		send_message(sockfd, "AUTH_REFUSE\n");
		if(get_message(sockfd, &buff) < 0)
			return -1;
		if(strstr(buff, "ACK_REFUSE"))
			return 0;
	}
    free(buff);
	return 0;
}
/*
// for testing
EVENT_T *db_get_recent_events(int *n, EVENT_T *events) {
	*n = 5;
	events = malloc(sizeof(EVENT_T) * (*n));
	
	char *buff = malloc(sizeof(char) * MAX);
	strcpy(buff, "111222\n");
	char *buff1 = malloc(sizeof(char) * MAX);
	strcpy(buff1, "2015-12-12\n");
	char *buff2 = malloc(sizeof(char) * MAX);
	strcpy(buff2, "15:16\n");

	for(int i=0; i<*n; i++) {
		events[i].event_id = buff;
		events[i].event_title = buff;
		events[i].event_organizer = buff;
		events[i].event_city = buff;
		events[i].event_date = buff1;
		events[i].event_time = buff2;
		events[i].event_deadline = buff1;
		events[i].event_description = buff;
		events[i].event_address = buff;
		events[i].event_capacity = buff;
		events[i].event_theme = buff;
		events[i].event_guest = buff;
		events[i].event_state = buff;
	}
	return events;
}*/

char *add_line_break(char* str) {
	char *res = malloc(sizeof(char) * (strlen(str) + 2));
	if(str[0] == '\0') {
		strcpy(res, "null\n");
	} else {
		strcpy(res, str);
		res[strlen(str)] = '\n';
		res[strlen(str)+1] = '\0';
	}
	return  res;
}

void get_recent_events_protocol(int sockfd, PGconn *db_conn) {
	int n;
	Event *events = NULL;
	send_message(sockfd, "ACK_GET_RECENT_EVENTS\n");
	char* buff = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	if(strstr(buff, "GET_EVENTS_NUM")) {
		events = db_get_recent_events(db_conn, &n, events);
		sprintf(buff, "%d\n", n);
		send_message(sockfd, buff);
		for(int i=0; i<n; i++) {
			send_message(sockfd, add_line_break(events[i].event_id));
			send_message(sockfd, add_line_break(events[i].event_title));
			send_message(sockfd, add_line_break(events[i].username_organizer));
			send_message(sockfd, add_line_break(events[i].event_city));
			send_message(sockfd, add_line_break(events[i].event_date));
			send_message(sockfd, add_line_break(events[i].event_time));
			send_message(sockfd, add_line_break(events[i].deadline_date));
			send_message(sockfd, add_line_break(events[i].description));
			send_message(sockfd, add_line_break(events[i].event_address));
			send_message(sockfd, add_line_break(events[i].capacity));
			send_message(sockfd, add_line_break(events[i].event_theme));
			send_message(sockfd, add_line_break(events[i].event_guest));
			send_message(sockfd, events[i].state);

			get_message(sockfd, &buff);
			if(!strstr(buff, "ACK_EVENT")) {
				printf("[%d]Event not delivered\n", getpid());
				break;
			}
		}
		send_message(sockfd, "EVENTS_END\n");
	}
	free(buff);
}

Event *db_get_search_result(int *n, Event *events, char* username, char* event_name, char* start_date, char* end_date, int *state) {
	*state = 1;
	*n = 2;
	events = malloc(sizeof(Event) * (*n));
	
	char *buff = malloc(sizeof(char) * MAX);
	strcpy(buff, "111222\n");
	char *buff1 = malloc(sizeof(char) * MAX);
	strcpy(buff1, "2015-12-12\n");
	char *buff2 = malloc(sizeof(char) * MAX);
	strcpy(buff2, "15:16\n");

	for(int i=0; i<*n; i++) {
		events[i].event_id = buff;
		events[i].event_title = buff;
		events[i].username_organizer = buff;
		events[i].event_city = buff;
		events[i].event_date = buff1;
		events[i].event_time = buff2;
		events[i].deadline_date = buff1;
		events[i].description = buff;
		events[i].event_address = buff;
		events[i].capacity = buff;
		events[i].event_theme = buff;
		events[i].event_guest = buff;
		events[i].state = buff;
	}
	return events;
}

void get_search_events_protocol(int sockfd, PGconn* db_conn) {
	int n;
	Event *events = NULL;
	send_message(sockfd, "ACK_GET_SEARCH_EVENTS\n");
	char* buff = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	char delim[] = " ";
	char* username  = strtok(buff, delim);
	char* event_name  = strtok(NULL, delim);
	char* start_date  = strtok(NULL, delim);
	char* end_date  = strtok(NULL, delim);
	int state;
	//events = search_events(db_conn , &n, &state, events, username, event_name, start_date, end_date, "");
	events = db_get_search_result(&n, events, username, event_name, start_date, end_date, &state);
	if(state > 0) {
		send_message(sockfd, "VALID_SEARCH_QUERY\n");
		get_message(sockfd, &buff);
		if(strstr(buff, "GET_EVENTS_NUM")) {
			sprintf(buff, "%d\n", n);
			send_message(sockfd, buff);
			for(int i=0; i<n; i++) {
				send_message(sockfd, add_line_break(events[i].event_id));
				send_message(sockfd, add_line_break(events[i].event_title));
				send_message(sockfd, add_line_break(events[i].username_organizer));
				send_message(sockfd, add_line_break(events[i].event_city));
				send_message(sockfd, add_line_break(events[i].event_date));
				send_message(sockfd, add_line_break(events[i].event_time));
				send_message(sockfd, add_line_break(events[i].deadline_date));
				send_message(sockfd, add_line_break(events[i].description));
				send_message(sockfd, add_line_break(events[i].event_address));
				send_message(sockfd, add_line_break(events[i].capacity));
				send_message(sockfd, add_line_break(events[i].event_theme));
				send_message(sockfd, add_line_break(events[i].event_guest));
				send_message(sockfd, events[i].state);

				get_message(sockfd, &buff);
				if(!strstr(buff, "ACK_EVENT")) {
					printf("[%d]Event not delivered\n", getpid());
					break;
				}
			}
			send_message(sockfd, "EVENTS_END\n");
		}
	}
	free(buff);
}

void add_user_protocol(int sockfd, PGconn* db_conn) {
	send_message(sockfd, "ACK_ADD_USER\n");
	
	char* buff = malloc(sizeof(char) * MAX);
	USER_T user_info;
	
	user_info.l_name = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(user_info.l_name, buff);

	user_info.f_name = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(user_info.f_name, buff);

	user_info.u_name = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(user_info.u_name, buff);

	user_info.pass = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(user_info.pass, buff);

	user_info.email = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(user_info.email, buff);

	user_info.tel = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(user_info.tel, buff);

	user_info.birth_place = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(user_info.birth_place, buff);

	user_info.birth_date = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(user_info.birth_date, buff);

	user_info.description = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(user_info.description, buff);
	
	int status_add = add_user(db_conn , user_info.u_name ,user_info.pass , user_info.email , user_info.l_name , user_info.f_name , user_info.description , user_info.birth_date, user_info.birth_place , user_info.tel, "");
	if(status_add < 0) {
		send_message(sockfd, "INVALID_USER_INFO\n");
	} else {
		send_message(sockfd, "VALID_USER_INFO\n");
		get_message(sockfd, &buff);
		if(strstr(buff, "ACK_VALID_USER_INFO")) {
			printf("[%d]Successfully created new user\n", getpid());
		}
	}
	free(buff);
	free(user_info.l_name);
	free(user_info.f_name);
	free(user_info.u_name);
	free(user_info.pass);
	free(user_info.email);
	free(user_info.tel);
	free(user_info.birth_place);
	free(user_info.birth_date);
	free(user_info.description);
}


void get_user_info_protocol(int sockfd, PGconn *db_conn) {
	char* buff = malloc(sizeof(char) * MAX);
	send_message(sockfd, "ACK_GET_USER_INFO\n");
	
	get_message(sockfd, &buff);
	
	USER_T user_info;
	get_user_infos(db_conn, buff, &user_info);
	send_message(sockfd, add_line_break(user_info.l_name));
	send_message(sockfd, add_line_break(user_info.f_name));
	send_message(sockfd, add_line_break(user_info.email));
	send_message(sockfd, add_line_break(user_info.tel));
	send_message(sockfd, add_line_break(user_info.birth_date));
	send_message(sockfd, add_line_break(user_info.birth_place));
	send_message(sockfd, add_line_break(user_info.description));
	
	get_message(sockfd, &buff);
	if(strstr(buff, "ACK_USER_INFO")) {
		printf("[%d]User info received by client\n", getpid());
	}
	free(buff);
}

int db_add_event(EVENT_T event_info) {
	return -1;
}

void add_event_protocol(int sockfd, PGconn *db_conn, char *login) {
	send_message(sockfd, "ACK_ADD_EVENT\n");
	
	char* buff = malloc(sizeof(char) * MAX);
	EVENT_T event_info;
	
	event_info.event_title = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(event_info.event_title, buff);

	event_info.event_theme = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(event_info.event_theme, buff);

	event_info.event_guest = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(event_info.event_guest, buff);

	event_info.event_date = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(event_info.event_date, buff);

	event_info.event_time = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(event_info.event_time, buff);

	event_info.event_deadline = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(event_info.event_deadline, buff);

	event_info.event_capacity = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(event_info.event_capacity, buff);

	event_info.event_description = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(event_info.event_description, buff);

	event_info.event_address = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	strcpy(event_info.event_address, buff);
	
	int ret_status = add_event(db_conn , event_info.event_time , event_info.event_date , event_info.event_address , "Cergy" , event_info.event_title , 
	event_info.event_theme , event_info.event_guest , event_info.event_description , atoi(event_info.event_capacity), "" , event_info.event_deadline , login);

	if(ret_status < 0) {
		send_message(sockfd, "INVALID_EVENT_INFO\n");
	} else {
		send_message(sockfd, "VALID_EVENT_INFO\n");
		get_message(sockfd, &buff);
		if(strstr(buff, "ACK_VALID_EVENT_INFO")) {
			printf("[%d]Successfully created new event\n", getpid());
		}
	}
	free(buff);
	free(event_info.event_title);
	free(event_info.event_theme);
	free(event_info.event_guest);
	free(event_info.event_date);
	free(event_info.event_time);
	free(event_info.event_deadline);
	free(event_info.event_capacity);
	free(event_info.event_description);
	free(event_info.event_address);
}

EVENT_T db_get_event_info(char* event_id, EVENT_T events) {
	char *buff = malloc(sizeof(char) * MAX);
	strcpy(buff, "111222\n");
	char *buff1 = malloc(sizeof(char) * MAX);
	strcpy(buff1, "2015-12-12\n");
	char *buff2 = malloc(sizeof(char) * MAX);
	strcpy(buff2, "15:16\n");

	events.event_id = buff;
	events.event_title = buff;
	events.event_organizer = buff;
	events.event_city = buff;
	events.event_date = buff1;
	events.event_time = buff2;
	events.event_deadline = buff1;
	events.event_description = buff;
	events.event_address = buff;
	events.event_capacity = buff;
	events.event_theme = buff;
	events.event_guest = buff;
	events.event_state = buff;
	return events;
}

MESSAGE_T* db_get_messages(int *n, MESSAGE_T* messages) {
	*n = 5;
	messages = malloc(sizeof(MESSAGE_T) * (*n));
	
	char *buff = malloc(sizeof(char) * MAX);
	strcpy(buff, "message\n");
	char *buff1 = malloc(sizeof(char) * MAX);
	strcpy(buff1, "2015-12-12\n");
	char *buff2 = malloc(sizeof(char) * MAX);
	strcpy(buff2, "15:16\n");
	char **buff3 = malloc(sizeof(char*) * 2);
	buff3[0] = malloc(sizeof(char) * MAX);
	strcpy(buff3[0], "user1\n");
	buff3[1] = malloc(sizeof(char) * MAX);
	strcpy(buff3[1], "user2\n");
	
	for(int i=0; i<*n; i++) {
		messages[i].message_content = buff;
		messages[i].message_writer = buff;
		messages[i].message_date = buff1;
		messages[i].message_time = buff2;
		messages[i].message_seen = buff3;
		messages[i].message_num_seen = 2;
	}
	return messages;
}

void get_messages_protocol(int sockfd) {
	char* buff = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	MESSAGE_T* messages = NULL;
	int n;
	messages = db_get_messages(&n, messages);
	if(strstr(buff, "GET_MESSAGES")) {
		sprintf(buff, "%d\n", n);
		send_message(sockfd, buff);
		for(int i=0; i<n; i++) {
			printf("%s\n", messages[i].message_content);
			send_message(sockfd, messages[i].message_writer);
			send_message(sockfd, messages[i].message_date);
			send_message(sockfd, messages[i].message_time);
			send_message(sockfd, messages[i].message_content);
			sprintf(buff, "%d\n", messages[i].message_num_seen);
			send_message(sockfd, buff);
			for(int j=0; j<messages[j].message_num_seen; j++) {
				send_message(sockfd, messages[i].message_seen[j]);
			}
			get_message(sockfd, &buff);
			if(!strstr(buff, "ACK_MESSAGE")) {
				break;
			}
		}
	}
	free(buff);
	free(messages[0].message_seen[0]);
	free(messages[0].message_seen[1]);
	free(messages[0].message_seen);
	free(messages[0].message_content);
	free(messages[0].message_date);
	free(messages[0].message_time);
	free(messages);

}

void get_event_info_protocol(int sockfd) {
	send_message(sockfd, "ACK_GET_EVENT_INFO\n");
	char* buff = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	EVENT_T event_info;
	event_info = db_get_event_info(buff, event_info);
	send_message(sockfd, event_info.event_id);
	send_message(sockfd, event_info.event_title);
	send_message(sockfd, event_info.event_organizer);
	send_message(sockfd, event_info.event_city);
	send_message(sockfd, event_info.event_date);
	send_message(sockfd, event_info.event_time);
	send_message(sockfd, event_info.event_deadline);
	send_message(sockfd, event_info.event_description);
	send_message(sockfd, event_info.event_address);
	send_message(sockfd, event_info.event_capacity);
	send_message(sockfd, event_info.event_theme);
	send_message(sockfd, event_info.event_guest);
	send_message(sockfd, event_info.event_state);
	
	get_message(sockfd, &buff);
	if(strstr(buff, "ACK_EVENT")) {
		get_messages_protocol(sockfd);
	}
}
int db_send_message(char* mes, char* login) {
	return 0;
}
void send_message_protocol(int sockfd, char* login) {
	send_message(sockfd, "ACK_SEND_MESSAGE\n");
	char* buff = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	if(db_send_message(buff, login) < 0) {
		send_message(sockfd, "MESSAGE_NOT_SENT\n");
	} else {
		send_message(sockfd, "MESSAGE_SENT\n");
	}
}

void func(int sockfd, PGconn* db_conn) {
	int authenticated = 0;
	char* login = malloc(sizeof(char) * 255);
	char *buff = malloc(sizeof(char) * MAX);

    while(1) {
		if(get_message(sockfd, &buff) < 0)
			break;
        if(strstr(buff, "AUTH")) {
			authenticated = authentication_protocol(sockfd, db_conn, &login);
			if(authenticated < 0)
				break;
		} else if(strstr(buff, "ADD_USER")) {
			add_user_protocol(sockfd, db_conn);
		} else if(strstr(buff, "EXIT")) {
			send_message(sockfd, "ACK_EXIT\n");
			break;
		} else if(strstr(buff, "GET_RECENT_EVENTS")) {
			if(authenticated) {
				get_recent_events_protocol(sockfd, db_conn);
			} else {
				send_message(sockfd, "NOT_AUTHENTICATED");
			}
		} else if(strstr(buff, "GET_SEARCH_EVENTS")) {
			if(authenticated) {
				get_search_events_protocol(sockfd, db_conn);
			} else {
				send_message(sockfd, "NOT_AUTHENTICATED");
			}
		} else if(strstr(buff, "GET_USER_INFO")) {
			if(authenticated) {
				get_user_info_protocol(sockfd, db_conn);
			} else {
				send_message(sockfd, "NOT_AUTHENTICATED");
			}
		} else if(strstr(buff, "ADD_EVENT")) {
			if(authenticated) {
				add_event_protocol(sockfd, db_conn, login);
			} else {
				send_message(sockfd, "NOT_AUTHENTICATED");
			}
		} else if(strstr(buff, "GET_EVENT_INFO")) {
			if(authenticated) {
				get_event_info_protocol(sockfd);
			} else {
				send_message(sockfd, "NOT_AUTHENTICATED");
			}
		} else if(strstr(buff, "SEND_MESSAGE")) {
			if(authenticated) {
				send_message_protocol(sockfd, login);
			} else {
				send_message(sockfd, "NOT_AUTHENTICATED");
			}
		} else {
			send_message(sockfd, "UNKNOWN_COMMAND\n");
		}
    } 
    free(buff);
    printf("[%d]Exiting..\n", getpid());
} 
  

int main(int argc, char** argv) {
	int PORT = 8080;
	if(argc == 2) {
		PORT = atoi(argv[1]);
		printf("%d\n", PORT);
	}
	PGconn *db_conn = db_connect();
    int sockfd, connfd;
    unsigned int len;
    pid_t ppid = getpid();
    struct sockaddr_in servaddr, cli; 

    sockfd = socket(AF_INET, SOCK_STREAM, 0); 
    if (sockfd == -1) { 
        printf("socket creation failed...\n"); 
        exit(0); 
    } 
    else
        printf("Socket successfully created..\n"); 
    bzero(&servaddr, sizeof(servaddr)); 
  
    servaddr.sin_family = AF_INET; 
    servaddr.sin_addr.s_addr = htonl(INADDR_ANY); 
    servaddr.sin_port = htons(PORT); 
  
    if ((bind(sockfd, (SA*)&servaddr, sizeof(servaddr))) != 0) {
        printf("socket bind failed...\n"); 
        exit(0); 
    } 
    else
        printf("Socket successfully binded..\n"); 
  

    if ((listen(sockfd, 5)) != 0) {
        printf("Listen failed...\n"); 
        exit(0); 
    }
    else
        printf("Server listening..\n"); 
    len = sizeof(cli); 
	int client_count = 0;
	while(1) {
		client_count ++;
		connfd = accept(sockfd, (SA*)&cli, &len); 
		if (connfd < 0) {
			printf("server acccept failed...\n"); 
			exit(0);
		} else {
			printf("server acccept the client...\n"); 
			pid_t pid = fork();
			if(pid < 0) {
				printf("child process could not be created\n");
				exit(0);
			} else if(pid == 0) {
				printf("child process no %d successfully created\n", client_count);
				break;
			}
		}
	}
	if(getpid() != ppid) {
		func(connfd, db_conn); 
	} else {
		close(sockfd); 
	}
} 
