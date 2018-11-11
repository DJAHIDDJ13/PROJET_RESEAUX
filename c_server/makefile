CC=gcc
CFLAGS=-Wall -g

main: main.o
	$(CC) -o main main.o

%.o: %.c 
	indent -kr $< 
	$(CC) -c $< $(CFLAGS)

.PHONY: clean
clean:
	rm -f *.o main 
