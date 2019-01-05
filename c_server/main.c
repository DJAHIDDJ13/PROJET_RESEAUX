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

#define MAX 2048 
#define PORT 8080 
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
	char* l_name;
	char* f_name;
	char* u_name;
	char* pass;
	char* email;
	char* tel;
	char* birth_place;
	char* birth_date;
	char* description;
} USER_T;


void send_message(int sockfd, char* mes) {
	if(write(sockfd, mes, strlen(mes)) != strlen(mes)) {
		printf("[%d] Could not send response\n", getpid());
		exit(0);
	}
	printf("[%d]Sent: %s\n", getpid(), mes);
}

void read_line(int sockfd, char** buff) {
	char c;
	int n = 0;
	do {
		if(read(sockfd, &c, 1) < 0)
			printf("[%d]Read error\n", getpid());
		*buff[n++] = c;
		printf("%c\n", c);
	} while(c != '\n' && c != '\r' && c != '\0'&& n < MAX);
}

int get_message(int sockfd, char** buff) {
	memset(*buff, 0, sizeof(char) * MAX);
	fd_set set;
	struct timeval timeout;
	int rv;

	FD_ZERO(&set); /* clear the set */
	FD_SET(sockfd, &set); /* add our file descriptor to the set */

	timeout.tv_sec = 8*60*60;
	timeout.tv_usec = 0;

	rv = select(sockfd + 1, &set, NULL, NULL, &timeout);
	if(rv == -1) {
		printf("[%d]Select\n", getpid()); /* an error accured */
		return -1;
	} else if(rv == 0) {
		send_message(sockfd, "TIMEOUT_EXIT\n");
		printf("[%d]Timeout\n", getpid()); /* a timeout occured */
		return -1;
	} else {
		printf("Reading\n");
		read_line(sockfd, buff);
	}
 	printf("[%d]Received: *%s*\n", getpid(), *buff);
 	return 0;
}

int is_valid_login(char* login, char* pass) {
	return 1;
}

int authentication_protocol(int sockfd, char **username) {
	char *buff = malloc(sizeof(char) * MAX);
	send_message(sockfd, "ACK_AUTH\n");
	
	if(get_message(sockfd, &buff) < 0)
		return -1;
	char delim[] = " ";
	buff[strlen(buff) - 1] = '\0';
	char *login = strtok(buff, delim);
	char *pass = strtok(NULL, delim);
	if(is_valid_login(login, pass)) {
		send_message(sockfd, "AUTH_ACCEPT\n");
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
    free(login);
    free(pass);
	return 0;
}
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
}

void get_recent_events_protocol(int sockfd) {
	int n;
	EVENT_T *events = NULL;
	send_message(sockfd, "ACK_GET_RECENT_EVENTS\n");
	char* buff = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	if(strstr(buff, "GET_EVENTS_NUM")) {
		events = db_get_recent_events(&n, events);
		sprintf(buff, "%d\n", n);
		send_message(sockfd, buff);
		for(int i=0; i<n; i++) {
			send_message(sockfd, events[i].event_id);
			send_message(sockfd, events[i].event_title);
			send_message(sockfd, events[i].event_organizer);
			send_message(sockfd, events[i].event_city);
			send_message(sockfd, events[i].event_date);
			send_message(sockfd, events[i].event_time);
			send_message(sockfd, events[i].event_deadline);
			send_message(sockfd, events[i].event_description);
			send_message(sockfd, events[i].event_address);
			send_message(sockfd, events[i].event_capacity);
			send_message(sockfd, events[i].event_theme);
			send_message(sockfd, events[i].event_guest);
			send_message(sockfd, events[i].event_state);

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

EVENT_T *db_get_search_result(int *n, EVENT_T *events, char* username, char* event_name, char* start_date, char* end_date, int *state) {
	*state = 1;
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
}
void get_search_events_protocol(int sockfd) {
	int n;
	EVENT_T *events = NULL;
	send_message(sockfd, "ACK_GET_SEARCH_EVENTS\n");
	char* buff = malloc(sizeof(char) * MAX);
	get_message(sockfd, &buff);
	char delim[] = " ";
	char* username  = strtok(buff, delim);
	char* event_name  = strtok(NULL, delim);
	char* start_date  = strtok(NULL, delim);
	char* end_date  = strtok(NULL, delim);
	int state;
	events = db_get_search_result(&n, events, username, event_name, start_date, end_date, &state);
	if(state > 0) {
		send_message(sockfd, "VALID_SEARCH_QUERY\n");
		get_message(sockfd, &buff);
		if(strstr(buff, "GET_EVENTS_NUM")) {
			events = db_get_recent_events(&n, events);
			sprintf(buff, "%d\n", n);
			send_message(sockfd, buff);
			for(int i=0; i<n; i++) {
				send_message(sockfd, events[i].event_id);
				send_message(sockfd, events[i].event_title);
				send_message(sockfd, events[i].event_organizer);
				send_message(sockfd, events[i].event_city);
				send_message(sockfd, events[i].event_date);
				send_message(sockfd, events[i].event_time);
				send_message(sockfd, events[i].event_deadline);
				send_message(sockfd, events[i].event_description);
				send_message(sockfd, events[i].event_address);
				send_message(sockfd, events[i].event_capacity);
				send_message(sockfd, events[i].event_theme);
				send_message(sockfd, events[i].event_guest);
				send_message(sockfd, events[i].event_state);

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
int db_add_user(USER_T user_info) {
	return -1;
}
void add_user_protocol(int sockfd) {
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

	if(db_add_user(user_info) < 0) {
		send_message(sockfd, "INVALID_USER_INFO\n");
	} else {
		send_message(sockfd, "VALID_USER_INFO\n");
	}
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
// Function designed for chat between client and server. 
void func(int sockfd) {
	int authenticated = 0;
	char* login = malloc(sizeof(char) * 255);
	char *buff = malloc(sizeof(char) * MAX);
    // infinite loop for chat 
    while(1) {
        // read the message from client and copy it in buffer 
		if(get_message(sockfd, &buff) < 0)
			break;
        if(strstr(buff, "AUTH")) {
			authenticated = authentication_protocol(sockfd, &login);
			if(authenticated < 0)
				break;
		} else if(strstr(buff, "ADD_USER")) {
			add_user_protocol(sockfd);
		} else if(strstr(buff, "EXIT")) {
			send_message(sockfd, "ACK_EXIT\n");
			break;
		} else if(strstr(buff, "GET_RECENT_EVENTS")) {
			if(authenticated) {
				get_recent_events_protocol(sockfd);
			}
		} else if(strstr(buff, "GET_SEARCH_EVENTS")) {
			if(authenticated) {
				get_search_events_protocol(sockfd);
			}
		} else {
			send_message(sockfd, "UNKNOWN_COMMAND\n");
		}
    } 
    free(buff);
    printf("[%d]Exiting..\n", getpid());
} 
  
// Driver function 
int main() {
    int sockfd, connfd;
    unsigned int len;
    pid_t ppid = getpid();
    struct sockaddr_in servaddr, cli; 
    // socket create and verification 
    sockfd = socket(AF_INET, SOCK_STREAM, 0); 
    if (sockfd == -1) { 
        printf("socket creation failed...\n"); 
        exit(0); 
    } 
    else
        printf("Socket successfully created..\n"); 
    bzero(&servaddr, sizeof(servaddr)); 
  
    // assign IP, PORT 
    servaddr.sin_family = AF_INET; 
    servaddr.sin_addr.s_addr = htonl(INADDR_ANY); 
    servaddr.sin_port = htons(PORT); 
  
    // Binding newly created socket to given IP and verification 
    if ((bind(sockfd, (SA*)&servaddr, sizeof(servaddr))) != 0) {
        printf("socket bind failed...\n"); 
        exit(0); 
    } 
    else
        printf("Socket successfully binded..\n"); 
  
    // Now server is ready to listen and verification 
    if ((listen(sockfd, 5)) != 0) {
        printf("Listen failed...\n"); 
        exit(0); 
    }
    else
        printf("Server listening..\n"); 
    len = sizeof(cli); 
	int client_count = 0;
    // Accept the data packet from client and verification 
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
	// Function for chatting between client and server
	if(getpid() != ppid) {
		func(connfd); 
	} else {
		close(sockfd); 
	}
} 
