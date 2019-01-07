#ifndef FUNC_UTIL_H
#define FUNC_UTIL_H

#include <libpq-fe.h>
typedef enum {true, false} bool;
/*structure events */

typedef struct{
	char* event_id;
	char* event_time;
	char* event_date;
	char* event_address;
	char* event_city;
	char* event_title;
	char* event_theme;
	char* event_guest;
	char* description;
	char* capacity;
	char* event_picture;
	char* confirmed;
	char* deadline_date;
	char* proposition_date;
	char* confirmation_date;
	char* modification_date;
	char* deletion_date;
	char* username_organizer;
	char* discussion_id;
	char* state;
} Event;

PGconn* db_connect();
bool test_login(PGconn *conn ,char* username , char* password );

int add_user(PGconn *conn ,char* username ,char* password , char* email , char* last_name , char* first_name 
,char* description , char* birthday_user, char* place_of_birth ,char* phone_number, char* userPicture);

int add_event(PGconn *conn , char* time , char* date , char* adress , char* city , char* title , char* theme , char* guest , char* description 
, int capacity  , char* event_picture , char* deadline , char* organizer);


Event *db_get_recent_events(PGconn *conn, int *n, Event *events);

Event *search_events( PGconn *conn , int *n, int* state, Event *events, char* username, char* event_title, char* start_date, char* end_date, char* event_theme);
#endif
