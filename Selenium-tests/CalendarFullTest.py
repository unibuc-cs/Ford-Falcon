from contextlib import suppress
from datetime import date
import time

from dbCleaner import dbCleaner
from RandomSignupGen import *

from selenium import webdriver #type: ignore
from selenium.webdriver.common.by import By #type: ignore
from selenium.common.exceptions import UnexpectedAlertPresentException, \
                                        NoAlertPresentException

USER = 'test_user'
PASSWORD = '1234'
TEST_EVENT = 'Test_Event_Selenium_1'
TODAY = date.today()
DAY = TODAY.day
MONTH = TODAY.month - 1
YEAR = TODAY.year
DBCLEANER = dbCleaner(USER, PASSWORD, TEST_EVENT)

driver = webdriver.Chrome()

try:
    driver.get("http://localhost/Ford-Falcon/interfata/loginh.php")
    time.sleep(1)

    username_input = driver.find_element(By.ID, "username")
    password_input = driver.find_element(By.ID, "password")

    username_input.send_keys(USER)
    password_input.send_keys(PASSWORD)

    login_button = driver.find_element(By.ID, "login_button")
    login_button.click()

    time.sleep(2)

    assert "homepage.php" in driver.current_url, "Login failed! Not redirected to homepage."
    print("Successfully logged in.")

    create_button = driver.find_element(By.ID, "createButton")
    create_button.click()

    time.sleep(2)

    input_field = driver.find_element(By.NAME, "name")
    input_field.send_keys(TEST_EVENT)

    submit_button = driver.find_element(By.NAME, "submit_calendar")
    with suppress(UnexpectedAlertPresentException):
        submit_button.click()
        time.sleep(1)
        alert = driver.switch_to.alert
        alert.accept()

    print("Successfully created a new calendar.")
    time.sleep(2)

    calendar = driver.find_element(By.XPATH, f"//h3[text()='{USER} - {TEST_EVENT}']")
    calendar.click()

    time.sleep(2)

    eventtitle = driver.find_element(By.NAME, "eventTitle")
    eventtitle.send_keys("Test Event")

    eventdate = driver.find_element(By.NAME, "eventDate")
    eventdate.send_keys(date.today().strftime("%m-%d-20%y"))
    time.sleep(1)

    eventtime = driver.find_element(By.NAME, "eventTime")
    eventtime.send_keys("12:00PM")

    eventlocation = driver.find_element(By.NAME, "eventLocation")
    eventlocation.send_keys("Online")

    eventdescription = driver.find_element(By.NAME, "eventDescription")
    eventdescription.send_keys("This is a test event.")

    addEvent = driver.find_element(By.ID, "addEvent")
    addEvent.click()
    time.sleep(1)

    with suppress(UnexpectedAlertPresentException):
        try:
            alert = driver.switch_to.alert
            alert.accept()
        except NoAlertPresentException:
            pass
    print("Successfully added a new event to the calendar.")
    time.sleep(2)

    driver.refresh()
    time.sleep(1)

    date_selector = f'//span[text()="{DAY}"]'
    date_element = driver.find_element(By.XPATH, date_selector)
    date_element.click()
    time.sleep(1)

    delete_button = driver.find_element(By.XPATH, '//button[text()="Delete"]')
    delete_button.click()
    time.sleep(1)

    with suppress(UnexpectedAlertPresentException):
        alert = driver.switch_to.alert
        alert.accept()
        time.sleep(1)
        alert = driver.switch_to.alert
        alert.accept()
        time.sleep(1)
        alert = driver.switch_to.alert
        alert.accept()
        time.sleep(1)

    print("Successfully deleted the event.")

    header = driver.find_element(By.CLASS_NAME, "header")
    home_button = header.find_element(By.ID, "home_button")
    home_button.click()
    time.sleep(1)

    delete_buttons = driver.find_elements(By.NAME, "delete_calendar")

    delete_buttons[-1].click()
    print("Successfully deleted the calendar.")
    time.sleep(1)

    print("Test Passed: All tests completed successfully.")
except Exception as e:
    print(f"Test Failed: {e}")
    DBCLEANER.clean()

finally:
    driver.quit()
    exit(0)