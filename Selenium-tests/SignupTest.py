import time
from typing import Final

from RandomSignupGen import *

import mysql.connector  # type: ignore
from selenium import webdriver #type: ignore
from selenium.webdriver.common.by import By #type: ignore

USERNAME : Final[str] = generate_random_username()
PASSWORD : Final[str] = generate_random_password()
EMAIL_ADDRESS : Final[str] = generate_random_email(USERNAME)

driver = webdriver.Chrome()

try:
    driver.get("http://localhost/Ford-Falcon/interfata/loginh.php")

    time.sleep(2)
    signup_link = driver.find_element(By.ID, "signup_link")
    signup_link.click()

    time.sleep(2)

    username_input = driver.find_element(By.NAME, "username")
    password_input = driver.find_element(By.NAME, "password")
    password_input_repeat = driver.find_element(By.NAME, "password-r")
    email = driver.find_element(By.NAME, "email")

    USERNAME = generate_random_username()
    PASSWORD = generate_random_password()
    EMAIL_ADDRESS = generate_random_email(USERNAME)

    username_input.send_keys(USERNAME)
    password_input.send_keys(PASSWORD)
    password_input_repeat.send_keys(PASSWORD)
    email.send_keys(EMAIL_ADDRESS)

    button = driver.find_element(By.NAME, "signup_button")
    button.click()
    time.sleep(2)

    db_connection = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="development"
    )

    cursor = db_connection.cursor()
    query = f"SELECT * FROM user WHERE username = \"{USERNAME}\""
    cursor.execute(query)
    user = cursor.fetchone()

    assert user is not None, "User creation failed! User not found in the database."
    print("Test Passed: User successfully created and found in the database.")

    query = f"DELETE FROM user WHERE username = \"{USERNAME}\""
    cursor.close()
    db_connection.close()

except Exception as e:
    print(f"Test Failed: {e}")

finally:
    driver.quit()
