#include <stdio.h>
#include <stdlib.h>
#include <libpq-fe.h>
#include <string.h>
#include <unistd.h>
#include "func_util.h"

#define t_MAX 1000


void do_exit(PGconn *conn) {
    PQfinish(conn);
}

/* int to char */

char* convert_int(int i){
	char* str = malloc (sizeof(char));
	sprintf(str,"%d",i);
	return str ;
}

/* get different theme*/

/*char** get_all_themes(PGconn *conn){

    char *themes[5];
    PGresult *res = PQexec(conn, "SELECT unnest(enum_range(NULL::theme))  ");    
    
    if (PQresultStatus(res) != PGRES_TUPLES_OK) {

        printf("No data retrieved\n");        
        PQclear(res);
        do_exit(conn);
    }       
    
    int rows = PQntuples(res);
    
    for(int i=0; i<rows; i++) {
        
        themes[i]= malloc(strlen(PQgetvalue(res, i, 0)) +1);
	strcpy(themes[i],PQgetvalue(res, i, 0));
    }
	return *themes[5];
}*/

/*get different guest

char* get_all_guests(PGconn *conn , char *guests){

    guests = malloc(sizeof(char*) * 2);
    PGresult *res = PQexec(conn, "SELECT unnest(enum_range(NULL::guest))  ");    
    
    if (PQresultStatus(res) != PGRES_TUPLES_OK) {

        printf("No data retrieved\n");        
        PQclear(res);
        do_exit(conn);
    }       
    
    int rows = PQntuples(guests);
    
    for(int i=0; i<rows; i++) {

        guests[i] = PQgetvalue(res, i, 0);
    }
	
}

*/



/* connexion : verfier username et login */

bool test_login(PGconn *conn ,char* username , char* password ){
	const char *values_up[2] = {(char *)password ,(char *)username};
	PGresult *res_p = PQexecParams(conn,
	"SELECT user_password = crypt($1,user_password) FROM account where username=$2 ",
	2, //number of parameters
	NULL, //ignore the Oid field
	values_up, //values to substitute $1 and $2
	NULL, //the lengths, in bytes, of each of the parameter values
	NULL, //whether the values are binary or not
	0); //we want the result in text format

	if (PQresultStatus(res_p) != PGRES_TUPLES_OK) {
		fprintf(stderr, "[%d]SELECT failed: %s", getpid(), PQerrorMessage(conn));
		PQclear(res_p);	
		return false; 
	} else {
		if(strcmp(PQgetvalue(res_p, 0, 0),"t") == 0){
			return true ;
		}
		return false;
	}	
	return false;
}


/*create new user */
int add_user(PGconn *conn ,char* username ,char* password , char* email , char* last_name , char* first_name ,char* description , char* birthday_user, char* place_of_birth ,char* phone_number, char* userPicture)
{

	printf("debut");
	PGresult *res = PQexec(conn, "SELECT CURRENT_DATE");    
    	char* now = PQgetvalue(res, 0, 0);
	
	const char *values_a[3] = {(char *)username, (char *)password ,"f"};
	
	PGresult *res2 = PQexecParams(conn,
	"INSERT INTO account(username, user_password, is_admin)	  			 	 	 		VALUES($1,$2,$3)",
	3, //number of parameters
	NULL, //ignore the Oid field
	values_a, //values to substitute $1 and $2
	NULL, //the lengths, in bytes, of each of the parameter values
	NULL, //whether the values are binary or not
	0); //we want the result in text format

	if (PQresultStatus(res2) != PGRES_COMMAND_OK) {
		fprintf(stderr, "SELECT failed: %s", PQerrorMessage(conn));
		PQclear(res2);
		return -1;	
	} else {
		const char *values_h[1] = {(char *)password};
		PGresult *res_h = PQexecParams(conn,
		"UPDATE account SET user_password = crypt($1, gen_salt('md5')) where user_password = $1",
		1, //number of parameters
		NULL, //ignore the Oid field
		values_h, //values to substitute $1 and $2
		NULL, //the lengths, in bytes, of each of the parameter values
		NULL, //whether the values are binary or not
		0); 
		if (PQresultStatus(res_h) != PGRES_COMMAND_OK){
			fprintf(stderr, "SELECT failed: %s", PQerrorMessage(conn));
			PQclear(res_h);
		}

		const char *values_u[13] = {(char *)username, (char *)email , (char *)last_name ,(char 	 		*)first_name  , (char *)description, birthday_user , (char *)place_of_birth, now , now ,(char  		*)phone_number , (char *)userPicture,"f","f"};
		
		
		PGresult *res3 = PQexecParams(conn,
		"INSERT INTO users(username, email, last_name, first_name, description, birthday_user, 		 	 place_of_birth, signup_date, modification_date,phone_number,user_picture,confirmed,connected) 		  		VALUES($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13)",
		13, //number of parameters
		NULL, //ignore the Oid field
		values_u, //values to substitute $1 and $2
		NULL, //the lengths, in bytes, of each of the parameter values
		NULL, //whether the values are binary or not
		0); //we want the result in text format

		if (PQresultStatus(res3) != PGRES_COMMAND_OK)
		{
			fprintf(stderr, "SELECT failed: %s", PQerrorMessage(conn));
			PQclear(res);
			return -1;
			
		}
	}
	return 0;

}

/*Create new Proposition */

int add_event(PGconn *conn , char* time , char* date , char* adress , char* city , char* title , char* theme , char* guest , char* description , int capacity  , char* event_picture , char* deadline , char* organizer)
{

	printf("debut");
	PGresult *res = PQexec(conn, "SELECT CURRENT_DATE");    
    	char* now = PQgetvalue(res, 0, 0);
	
	const char *values_d[1] = {now};
	
	PGresult *res2 = PQexecParams(conn,
	"INSERT INTO discussion(discussion_date)	  			 	 	 		VALUES($1) RETURNING discussion_id",
	1, //number of parameters
	NULL, //ignore the Oid field
	values_d, //values to substitute $1 and $2
	NULL, //the lengths, in bytes, of each of the parameter values
	NULL, //whether the values are binary or not
	0); //we want the result in text format

	if (PQresultStatus(res2) !=  PGRES_TUPLES_OK)
	{
		fprintf(stderr, "SELECT failed rs2: %s", PQerrorMessage(conn));
		PQclear(res2);
		return -1;
	}else{

	char *discussion_id = PQgetvalue(res2, 0, 0);
	char *capacity_c = convert_int(capacity);
	

	const char *values_e[16] = {(char *)time, (char *)date , (char *)adress ,(char 	 		 	*)city  , (char *)title , (char *)theme, (char*)guest , (char *) description, (char *)capacity_c , 	(char *)event_picture ,"f", now , now , (char *)deadline , organizer ,discussion_id};
	
	
	PGresult *res3 = PQexecParams(conn,"INSERT INTO events(event_time, event_date, event_address, 		event_city, event_title, event_theme, event_guest, description, capacity, event_picture, 		confirmed, proposition_date, modification_date, deadline_date,username_organizer,discussion_id) 			VALUES($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16)",
	16, //number of parameters
	NULL, //ignore the Oid field
	values_e, //values to substitute $1 and $2
	NULL, //the lengths, in bytes, of each of the parameter values
	NULL, //whether the values are binary or not
	0); //we want the result in text format

		if (PQresultStatus(res3) != PGRES_COMMAND_OK)
		{
			fprintf(stderr, "SELECT failed: %s", PQerrorMessage(conn));
			PQclear(res3);
			return -1;
		}
	}
	
	return 0;
}

// recent events 
Event *db_get_recent_events(PGconn *conn , int *n, Event *events) {
	events = malloc( sizeof(Event) * 5);
	PGresult *res = PQexec(conn, "SELECT * FROM events where confirmation_date IS NOT NULL and event_date >= (SELECT CURRENT_DATE) and deletion_date IS NULL ORDER BY proposition_date desc LIMIT 5");    

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
		printf("No data retrieved\n");        
		PQclear(res);
		do_exit(conn);
    }       

    int rows = PQntuples(res);
    *n = rows;
    for(int i=0; i<rows; i++) {
		events[i].event_id = PQgetvalue(res, i,0); 
		events[i].event_time = PQgetvalue(res, i,1); 
		events[i].event_date = PQgetvalue(res, i,2); 
		events[i].event_address = PQgetvalue(res, i,3); 
		events[i].event_city = PQgetvalue(res, i,4); 
		events[i].event_title = PQgetvalue(res, i,5); 
		events[i].event_theme = PQgetvalue(res, i,6); 
		events[i].event_guest = PQgetvalue(res, i,7); 
		events[i].description = PQgetvalue(res, i,8); 
		events[i].capacity = PQgetvalue(res, i,9); 
		events[i].event_picture = PQgetvalue(res, i,10); 
		events[i].confirmed = PQgetvalue(res, i,11); 
		events[i].deadline_date = PQgetvalue(res, i,12); 
		events[i].proposition_date = PQgetvalue(res, i,13); 
		events[i].confirmation_date = PQgetvalue(res, i,14); 
		events[i].modification_date = PQgetvalue(res, i,15); 
		events[i].deletion_date = PQgetvalue(res, i,16); 
		events[i].username_organizer = PQgetvalue(res, i,17); 
		events[i].discussion_id = PQgetvalue(res, i,18); 
		events[i].state = malloc(sizeof(char) * 2048);
		strcpy(events[i].state, "NOT_POSSIBLE\n");
    }
	return events;
}

// search_events
Event *search_events(PGconn *conn , int* n, int *state, Event *events, char* username, char* event_title, char* start_date, char* end_date, char* event_theme) {
	PGresult *res;
	if(strcmp(event_theme, "") == 0) {
		const char *values_s[4] = { (char *)event_title, (char *)start_date , (char *)end_date ,(char *)username} ;

		res= PQexecParams(conn,
		"SELECT * FROM events where (STRPOS(event_title, $1)>0 OR $1='') AND (event_Date>=$2 OR $2 IS NULL) AND (event_Date>=$3 OR $3 IS NULL) AND 		(STRPOS(username_organizer, $4)>0 OR $4='')  AND confirmed AND deletion_date IS NULL",
		4, //number of parameters
		NULL, //ignore the Oid field
		values_s, //values to substitute $1 and $2
		NULL, //the lengths, in bytes, of each of the parameter values
		NULL, //whether the values are binary or not
		0); //we want the result in text format    
	} else {
		const char *values_s[5] = {(char*)event_title, (char*)start_date , (char*)end_date ,(char*)username , (char*)event_theme};
		 res= PQexecParams(conn,
		"SELECT * FROM events where (STRPOS(event_title, $1)>0 OR $1='') AND (event_Date>=$2 OR $2 IS NULL) AND (event_Date>=$3 OR $3 IS NULL) AND 		(STRPOS(username_organizer, $4)>0 OR $4='')  AND event_theme = $5 AND confirmed AND deletion_date IS NULL",
		4, //number of parameters
		NULL, //ignore the Oid field
		values_s, //values to substitute $1 and $2
		NULL, //the lengths, in bytes, of each of the parameter values
		NULL, //whether the values are binary or not
		0); //we want the result in text format  
	}
	
    
	    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
			printf("Aucun résultat\n");        
			PQclear(res);
			do_exit(conn);
			*state = -1;
			return NULL;
		}       
		int rows = PQntuples(res);
		*n = rows;
		events = malloc(sizeof(Event) *rows);

		for(int i=0; i<rows; i++) {
			events[i].event_id = PQgetvalue(res, i,0); 
			events[i].event_time = PQgetvalue(res, i,1); 
			events[i].event_date = PQgetvalue(res, i,2); 
			events[i].event_address = PQgetvalue(res, i,3); 
			events[i].event_city = PQgetvalue(res, i,4); 
			events[i].event_title = PQgetvalue(res, i,5); 
			events[i].event_theme = PQgetvalue(res, i,6); 
			events[i].event_guest = PQgetvalue(res, i,7); 
			events[i].description = PQgetvalue(res, i,8); 
			events[i].capacity = PQgetvalue(res, i,9); 
			events[i].event_picture = PQgetvalue(res, i,10); 
			events[i].confirmed = PQgetvalue(res, i,11); 
			events[i].deadline_date = PQgetvalue(res, i,12); 
			events[i].proposition_date = PQgetvalue(res, i,13); 
			events[i].confirmation_date = PQgetvalue(res, i,14); 
			events[i].modification_date = PQgetvalue(res, i,15); 
			events[i].deletion_date = PQgetvalue(res, i,16); 
			events[i].username_organizer = PQgetvalue(res, i,17); 
			events[i].discussion_id = PQgetvalue(res, i,18); 
			events[i].state = malloc(sizeof(char) * 2048);
			strcpy(events[i].state, "NOT_POSSIBLE\n");
		}
	*state = 1;
	return events ;
}



PGconn* db_connect() {
	
	PGconn *conn = PQconnectdb("user=postgres dbname=projetsortie");

	if (PQstatus(conn) == CONNECTION_BAD) {
	    
	    fprintf(stderr, "Connection to database failed: %s",
		PQerrorMessage(conn));
	    do_exit(conn);
	}
	return conn;
}



