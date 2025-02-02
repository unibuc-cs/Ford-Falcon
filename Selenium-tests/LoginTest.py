import time

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys

driver = webdriver.Chrome()

try:
    driver.get("http://localhost/Ford-Falcon/interfata/loginh.php")

    username_input = driver.find_element(By.ID, "username")
    password_input = driver.find_element(By.ID, "password")

    username_input.send_keys("test_user")
    password_input.send_keys("1234")
    password_input.send_keys(Keys.RETURN)

    time.sleep(2)

    assert "homepage.php" in driver.current_url, "Login failed! Not redirected to homepage."
    print("Test Passed: Successfully logged in and redirected to homepage.")

except (webdriver.NoSuchElementException, webdriver.WebDriverException) as e:
    print(f"Test Failed: {e}")

finally:
    driver.quit()
    exit(0)