-- TABLE : Account
INSERT INTO      
Account		(Username   ,User_Password                                                 ,Is_Admin)
VALUES
		('admin'    ,'$2y$10$jKKrDz6PD7gmfTBpMM5o1O0Dz.xSqO8yObk7ibjJ3OQXHwrlN3aFm',true     ),
		('adam123'  ,'$2y$10$gOORjg1VwFGJUqarKRz92eEeaavHAG4/lRGV6KczX65lNryak3x8K',false    ),
		('betty98'  ,'$2y$10$5nCC.w46d6GCEZ4MoMv1Wu8pGHAifK9gCkeknh6ORTZ/CafoMINNW',false    ),
		('carol666' ,'$2y$10$Ox7xAbDv01Hjn4q4rdiJYu8d.tFfsHsnDMx3p7hDBxVB8obJ2CJ46',false    ),
		('raphael42','$2y$10$F2du/i1gMHvy6jgQ3n95IeO6Xb/sFerpwJZW2WnkRlElmNLD/0bAa',false    ),
		('2018omar' ,'$2y$10$fkAMwsYFWBX8MzR84lXauevtroSfBrj0jcwU2c6W8zzd6WuCYxhcK',false    ),
		('chris2999','$2y$10$uZg1Kg4sgdndmT9tT78rteBEM8hgX2kqL0d.aayg6kVYMlk9CvX0u',false    );


-- hashfunc = bcrypt
-- unhashed passwords: admin, pass123, 123456, starwars, azerty123, dragon, football


-- Table : Users
INSERT INTO 
Users	(Username   ,Email                 ,Last_Name,First_name,Description               ,Birthday_User,Phone_Number,Place_of_Birth,connected,confirmed,User_Picture,Confirmation_Date,Signup_Date ,Modification_Date,Deletion_Date)
VALUES 
		('adam123'  ,'Adam123@gmail.com'   ,'west'   ,'adam'    ,null                      ,'1989/01/10' ,'0675475644','Pontoise'    ,true     ,true     ,null        ,'2018/10/16'     ,'2018/10/16','2018/10/16'     ,null         ),
		('betty98'  ,'Betty98@hotmail.com' ,'crocker','betty'   ,'I like Traveling'        ,'1995/04/25' ,null        ,'Cergy'       ,true     ,true     ,null        ,'2018/10/19'     ,'2018/10/19','2018/10/19'     ,null         ),
		('carol666' ,'Carol666@hotmail.com','smith'  ,'carol'   ,null                      ,'1998/09/15' ,'0998405185','Versailles'  ,true     ,true     ,null        ,'2018/10/26'     ,'2018/10/24','2018/10/27'     ,null         ),
		('raphael42','Raphael42@yahoo.fr'  ,'nadal'  ,'raphael' ,'I like going to concerts','1983/02/03' ,null        ,'Cergy'       ,true     ,true     ,null        ,'2018/10/10'     ,'2018/10/10','2018/10/10'     ,null         ),
		('2018omar' ,'2018omar@gmail.com'  ,'epps'   ,'omar'    ,null                      ,null         ,null        ,null          ,false    ,false    ,null        ,null             ,'2018/10/09','2018/10/25'     ,'2018/10/25' ),
		('chris2999','Chris2999@gmail.com' ,'griffin','chris'   ,null                      ,'2000/12/19' ,'0769784516','Paris'       ,true     ,true     ,null        ,'2018/09/09'     ,'2018/09/08','2018/09/09'     ,null         );

-- Table : Invitation 
INSERT INTO 
Invitation 	(Invitation_Date,Acceptance_Date,acceptance_time,username_receiver,username_sender)
VALUES 
		('2018/10/20'   ,'2018/10/20'   ,'12:54'        ,'betty98'        ,'adam123'      ),
		('2018/10/24'   ,'2018/10/25'   ,'14:25'        ,'carol666'       ,'adam123'      ),
		('2018/10/16'   ,'2018/10/20'   ,'9:25'         ,'raphael42'      ,'adam123'      ),
		('2018/10/19'   ,'2018/10/20'   ,'5:14'         ,'betty98'        ,'betty98'      ),
		('2018/10/24'   ,null           ,null           ,'2018omar'       ,'carol666'     ),
		('2018/10/26'   ,'2018/10/26'   ,'15:51'        ,'chris2999'      ,'carol666'     ),
		('2018/10/26'   ,'2018/10/26'   ,'16:16'        ,'betty98'        ,'carol666'     ),
		('2018/10/16'   ,'2018/10/16'   ,'19:15'        ,'adam123'        ,'chris2999'    );
		
-- Table : Theme

INSERT INTO 
Theme		(theme_title )
VALUES
		('concert'   ),
		('exposition'),
		('festival'  ),
		('concours'  ),
		('autre'     );


-- Table : Guest 

INSERT INTO 
Guest		(guest_title	 )
VALUES 
		('Tout le Monde'),
		('Mes Amis'     );


-- Table : Discussion 

INSERT INTO 
Discussion 	(discussion_date)
VALUES 
		('2018/10/17'   ),
		('2018/10/17'   ),
		('2018/10/27'   ),
		('2018/10/24'   );

-- Table : Event 

INSERT INTO 
Events  	   (event_time,event_date  ,event_address                             ,event_city ,event_title   ,description                                                             ,capacity,event_picture,proposition_date,confirmation_date,modification_date,deletion_date,confirmed        ,theme_id,deadline_date,guest_id,username_organizer,discussion_id)

VALUES 
		   ('18:00'   ,'2019/02/02','1 carrefour de Longchamp-75016 Paris'    ,'Paris'    ,'Human'       ,'Exposition audiovisuel sur les effets de l''etre humain sur la planète',1000    ,null         ,'2018/10/17'    ,'2018/10/17'             ,'2018/10/17'     ,null         ,false            ,2       ,'2018/12/01' ,1       ,'adam123'         ,1 	   ),
		   ('14:30'   ,'2019/02/12','Accorhotels arena 8 bd de bercy paris 12','Paris'    ,'Indochine 13','Concert de l''artiste Indochine dans son 13 eme tour'                  ,10000   ,null         ,'2018/10/17'    ,'2018/10/17'             ,'2018/10/17'     ,null         ,false            ,1       ,'2018/12/11' ,1       ,'adam123'         ,2   	   ),
		   ('23:59'   ,'2999/12/31','A'                                       ,'Barcelone','A'           ,null                                                                    ,99999   ,null         ,'2018/10/24'    ,'2018/10/24'             ,'2018/10/25'     ,'2018/10/25' ,false            ,4       ,'2018/12/31' ,1       ,'2018omar'        ,4	       ),
		   ('08:00'   ,'2019/01/16','Île de loisirs de Cergy-Pontoise'        ,'Cergy'    ,'Sortie Parc' ,'On va sortir vers le parc des loisirs'                                 ,200     ,null         ,'2018/10/27'    ,'2018/10/27'     ,'2018/10/30'     ,null         ,true             ,5       ,'2018/11/16' ,2       ,'betty98'         ,3  	           );

-- Table : Notification 

INSERT INTO 
Notification 	(notification_content                                               ,notification_date,notification_time,seen ,username_receiver)
VALUES
		('Votre proposition de sortie intitulé "A" à été refusé'            ,'2018/10/25'     ,'15:13'          ,true ,'2018omar' 	),
		('Vous avez été banni de façon permanente'                          ,'2018/10/25'     ,'15:14'          ,true ,'2018omar'	),
		('Votre proposition de sortie intitulé "Human" à été accepté'       ,'2018/10/17'     ,'16:00'          ,true ,'adam123' 	),
		('Votre proposition de sortie intitulé "Indochine 13" a été accepté','2018/10/17'     ,'17:16'          ,true ,'adam123'  	),
		('Votre proposition de sortie intitulé "sortie parc" à été accepté' ,'2018/10/27'     ,'17:21'          ,false,'betty98'  	),
		('betty98 à accepter votre invitation'                              ,'2018/10/20'     ,'12:54'          ,true ,'adam123'  	),
		('carol666 à accepter votre invitation'                             ,'2018/10/25'     ,'14:25'          ,true ,'adam123'  	),
		('raphael42 à accepter votre invitation'                            ,'2018/10/20'     ,'09:25'          ,true ,'adam123'  	),
		('raphael42 à accepter votre invitation'                            ,'2018/10/20'     ,'05:14'          ,true ,'betty98'  	),
		('chris2999 à accepter votre invitation'                            ,'2018/10/26'     ,'15:51'          ,true ,'carol666' 	),
		('betty98 à accepter votre invitation'                              ,'2018/10/26'     ,'16:16'          ,true ,'carol666' 	),
		('adam123 à accepter votre invitation'                              ,'2018/10/16'     ,'19:15'          ,true ,'chris2999'	);

-- Table : Participate

INSERT INTO
Participate     (username_participant,event_id,subscription_date,unsubscription_date)
 VALUES 
		('adam123'  	     ,1       ,'2018/10/17'     ,null        ),
		('adam123'  	     ,2       ,'2018/10/17'     ,null        ),
		('adam123'  	     ,4       ,'2018/10/27'     ,null        ),
		('betty98'  	     ,2       ,'2018/10/20'     ,'2018/10/20'),
		('betty98'  	     ,4       ,'2018/10/27'     ,null        ),
		('carol666' 	     ,1       ,'2018/10/26'     ,null        ),
		('carol666' 	     ,4       ,'2018/10/28'     ,null        ),
		('raphael42'	     ,1       ,'2018/10/18'     ,null        ),
		('raphael42'	     ,2       ,'2018/10/18'     ,null        ),
		('chris2999'	     ,2       ,'2018/10/19'     ,null        );

-- Table : Message 

INSERT INTO 
Message 	(Message_Content			     ,Sending_Date,Sending_Time,Username_Transmitter,Discussion_ID)

VALUES 
		('Bienvenue a cette sortie'               ,'2018/10/17','15:03'     ,'adam123'           ,1            ),
		('Merci'                                  ,'2018/10/26','15:10'     ,'carol666'          ,1            ),
		('Bonjour'                                ,'2018/10/18','15:00'     ,'adam123'           ,2            ),
		('Salut'                                  ,'2018/10/18','16:01'     ,'raphael42'         ,2            ),
		('cc'                                     ,'2018/10/19','10:00'     ,'chris2999'         ,2            ),
		('Bonjour'                                ,'2018/10/28','13:54'     ,'carol666'          ,3            ),
		('c est pour quelle heure cette sortie'   ,'2018/10/28','13:56'     ,'carol666'          ,3            ),
		('Salut,c est pour le 16 novembre a 8:00' ,'2018/10/28','14:34'     ,'betty98'           ,3            );



-- Table : Receive 

INSERT INTO 
Receive 	(message_id,username_receiver,seen_time,seen_date   )
VALUES 
		(1	   ,'adam123'  	     ,'15:03'  ,'2018/10/17'),
		(1	   ,'carol666' 	     ,'15:09'  ,'2018/10/17'),
		(1	   ,'raphael42'	     ,'16:00'  ,'2018/10/17'),
		(2	   ,'adam123'  	     ,'15:11'  ,'2018/10/17'),
		(2	   ,'carol666' 	     ,'15:10'  ,'2018/10/17'),
		(2	   ,'raphael42'	     ,'16:00'  ,'2018/10/17'),
		(3	   ,'adam123'  	     ,'15:00'  ,'2018/10/17'),
		(3	   ,'betty98' 	     ,null     ,null        ),
		(3	   ,'raphael42'	     ,'16:00'  ,'2018/10/17'),
		(3	   ,'chris2999'	     ,'09:59'  ,'2018/10/18'),
		(4	   ,'adam123'  	     ,'16:02'  ,'2018/10/17'),
		(4	   ,'betty98'  	     ,null     ,null        ),
		(4	   ,'raphael42'	     ,'16:01'  ,'2018/10/17'),
		(4	   ,'chris2999'	     ,'09:59'  ,'2018/10/18'),
		(5	   ,'adam123'  	     ,'12:00'  ,'2018/10/18'),
		(5	   ,'betty98'  	     ,null     ,null        ),
		(5	   ,'raphael42'	     ,'13:55'  ,'2018/10/18'),
		(5	   ,'chris2999'	     ,'09:59'  ,'2018/10/18'),
		(6	   ,'adam123'  	     ,'13:58'  ,'2018/10/27'),
		(6	   ,'betty98'  	     ,'14:30'  ,'2018/10/27'),
		(6	   ,'carol666' 	     ,'13:54'  ,'2018/10/27'),
		(7	   ,'adam123'  	     ,'13:58'  ,'2018/10/27'),
		(7	   ,'betty98'  	     ,'14:30'  ,'2018/10/27'),
		(7	   ,'carol666' 	     ,'13:56'  ,'2018/10/27'),
		(8	   ,'adam123'  	     ,'14:38'  ,'2018/10/27'),
		(8	   ,'betty98'        ,'14:34'  ,'2018/10/27'),
		(8	   ,'carol666' 	     ,'14:55'  ,'2018/10/27');

