#include <stdio.h>
#include <string.h>

int main() {
    char input[32];
    printf("Enter password: ");
    scanf("%s", input);

    if(strcmp(input, "reverse123") == 0) {
        printf("Flag: CTF{simple_reverse_elf}\n");
    } else {
        printf("Wrong!\n");
    }
    return 0;
}