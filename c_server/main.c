#include <netdb.h> 
#include <unistd.h>
#include <stdio.h>
#include <netinet/in.h> 
#include <stdlib.h> 
#include <string.h> 
#include <sys/socket.h> 
#include <sys/types.h> 
#include <time.h>

#define MAX_CLIENTS 10
#define MAX 500 
#define PORT 8080 
#define TIMEOUT 100
#define SA struct sockaddr 

void send_message(int sockfd, char* mes) {
	if(write(sockfd, mes, strlen(mes)) != strlen(mes)){
		printf("[%d] Could not send response\n", getpid());
		exit(0);
	}
	printf("[%d]Sent: %s\n", getpid(), mes);
}

//~ char* get_message(int sockfd) {
	//~ char *buff = malloc(sizeof(MAX));
	//~ char c;
	//~ int n = 0;
	//~ do {
		//~ read(sockfd, &c, 1);
		//~ buff[n++] = c;
	//~ } while(c != '\n' && c != '\r' && n < MAX-1);
	//~ buff[n-1] = '\0';
	//~ printf("[%d]Received: %s\n", getpid(), buff);
	//~ return buff;
//~ }

int get_message(int filedesc, char** buff) {
	fd_set set;
	struct timeval timeout;
	int rv;

	FD_ZERO(&set); /* clear the set */
	FD_SET(filedesc, &set); /* add our file descriptor to the set */

	timeout.tv_sec = 10;
	timeout.tv_usec = 0;

	rv = select(filedesc + 1, &set, NULL, NULL, &timeout);
	if(rv == -1) {
		printf("[%d]Select\n", getpid()); /* an error accured */
		return -1;
	}
	else if(rv == 0) {
		send_message(filedesc, "TIMEOUT_EXIT\n");
		printf("[%d]Timeout\n", getpid()); /* a timeout occured */
		return -1;
	}
	else
		read( filedesc, *buff, MAX ); /* there was data to read */
 	printf("[%d]Received: %s\n", getpid(), *buff);
 	return 0;
}

int is_valid_login(char* login, char* pass) {
	return 1;
}

char* split(char *mes) {
	int n = 0, c = 0;
	int len = strlen(mes);
	while((mes[n] == ' ' || c == 0) && n < len) {
		if(mes[n] == ' ') {
			c++;
		}
		n++;
	}
	return mes + n;
}

char *cut(char *mes) {
	for(int i=0; i<strlen(mes); i++)
		if(mes[i] == ' ') {
			mes[i] = 0;
			break;
		}
	return mes;
}

int authentication_protocol(int sockfd, char **username) {
	char *buff = malloc(sizeof(char) * MAX);
	send_message(sockfd, "ACK_AUTH\n");
	
	if(get_message(sockfd, &buff) < 0)
		return -1;

	char *pass  = split(buff);
	char *login = cut(buff);
	strcpy(*username, login);
	
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
	return 0;
}

void get_recent_events_protocol() {
	int n;
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
		} else if(strstr(buff, "EXIT")) {
			send_message(sockfd, "ACK_EXIT\n");
			break;
		} else if(strstr(buff, "GET_RECENT_EVENTS")) {
			if(authenticated) {
				get_recent_events_protocol(sockfd);
			}
		} else {
			send_message(sockfd, "UNKNOWN_COMMAND\n");
		}
    } 
    free(buff);
    printf("[%d]Exiting..\n", getpid());
    exit(0);
} 
  
// Driver function 
int main() {
    int sockfd, connfd, client_count = 0;
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
  
    // Accept the data packet from client and verification 
	while(client_count++ < MAX_CLIENTS) {
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
	}
	
    // After chatting close the socket 
    close(sockfd); 
} 
