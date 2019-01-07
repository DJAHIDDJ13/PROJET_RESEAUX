CC=gcc
CFLAGS=-Wall -g -L/usr/lib/x86_64-linux-gnu -I/usr/include/postgresql -lpq


main: main.o func_util.o
	$(CC) -o main main.o func_util.o $(CFLAGS)

main.o: main.c
	$(CC) -o main.o -c main.c $(CFLAGS)

func_util.o: func_util.c func_util.h
	$(CC) $(CFLAGS) -o func_util.o -c func_util.c

.PHONY: clean
clean:
	rm -f *.o main 
