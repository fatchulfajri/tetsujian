valid_flags = [
    "CTF{basic_sql_injection}"
]

flag = input("Submit flag: ")
if flag in valid_flags:
    print("Correct!")
else:
    print("Wrong!")