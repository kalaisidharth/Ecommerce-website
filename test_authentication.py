import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

# Setup
driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()))
wait = WebDriverWait(driver, 10)
BASE_URL = "http://localhost/ecommerce/"
# Use a unique email for each full test run
unique_email = f"john_{int(time.time())}@mail.com"

def test_registration_pass():
    """TC001: Tests successful user registration."""
    print("--- Running TC001: Successful Registration ---")
    driver.get(BASE_URL + "register.php")
    wait.until(EC.presence_of_element_located((By.NAME, "name"))).send_keys("John")
    driver.find_element(By.NAME, "email").send_keys(unique_email)
    driver.find_element(By.NAME, "password").send_keys("123456")
    driver.find_element(By.NAME, "role").send_keys("user")
    driver.find_element(By.CSS_SELECTOR, ".btn-primary").click()
    wait.until(EC.text_to_be_present_in_element((By.CLASS_NAME, "alert"), "Registration successful"))
    print("SUCCESS: TC001 Passed.")

def test_duplicate_registration_fail():
    """TC002: Tests registration with a duplicate email."""
    print("--- Running TC002: Duplicate Email Registration ---")
    driver.get(BASE_URL + "register.php")
    wait.until(EC.presence_of_element_located((By.NAME, "name"))).send_keys("John")
    driver.find_element(By.NAME, "email").send_keys(unique_email)
    driver.find_element(By.NAME, "password").send_keys("123456")
    driver.find_element(By.NAME, "role").send_keys("user")
    driver.find_element(By.CSS_SELECTOR, ".btn-primary").click()
    wait.until(EC.text_to_be_present_in_element((By.CLASS_NAME, "alert"), "Email already exists!"))
    print("SUCCESS: TC002 Passed.")

def test_login_pass():
    """TC003 & TC014: Tests valid user login and subsequent logout."""
    print("--- Running TC003 & TC014: Valid Login and Logout ---")
    driver.get(BASE_URL + "login.php")
    wait.until(EC.presence_of_element_located((By.NAME, "email"))).send_keys(unique_email)
    driver.find_element(By.NAME, "password").send_keys("123456")
    driver.find_element(By.CSS_SELECTOR, ".btn-success").click()
    wait.until(EC.url_contains("index.php"))
    print("SUCCESS: TC003 Passed.")
    driver.find_element(By.LINK_TEXT, "Logout").click()
    wait.until(EC.url_contains("login.php"))
    print("SUCCESS: TC014 Passed.")

def test_login_fail():
    """TC004: Tests login with an invalid password."""
    print("--- Running TC004: Invalid Login ---")
    driver.get(BASE_URL + "login.php")
    wait.until(EC.presence_of_element_located((By.NAME, "email"))).send_keys(unique_email)
    driver.find_element(By.NAME, "password").send_keys("wrongpass")
    driver.find_element(By.CSS_SELECTOR, ".btn-success").click()
    wait.until(EC.text_to_be_present_in_element((By.CLASS_NAME, "alert"), "Invalid credentials."))
    print("SUCCESS: TC004 Passed.")

if __name__ == "__main__":
    try:
        test_registration_pass()
        test_duplicate_registration_fail()
        test_login_pass()
        test_login_fail()
    finally:
        print("\nAuthentication test suite finished.")
        driver.quit()