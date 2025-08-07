import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

# Setup WebDriver automatically
driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()))
wait = WebDriverWait(driver, 10)
BASE_URL = "http://localhost/ecommerce/"

def test_registration():
    """Tests the new user registration process."""
    print("--- Running: test_registration ---")
    driver.get(BASE_URL + "register.php")
    # A unique email for each test run to avoid "email already exists"
    unique_email = f"testuser_{int(time.time())}@example.com"
    wait.until(EC.presence_of_element_located((By.NAME, "name"))).send_keys("Test User")
    driver.find_element(By.NAME, "email").send_keys(unique_email)
    driver.find_element(By.NAME, "password").send_keys("password123")
    driver.find_element(By.NAME, "role").send_keys("user")
    driver.find_element(By.CSS_SELECTOR, ".btn-primary").click()
    wait.until(EC.text_to_be_present_in_element((By.CLASS_NAME, "alert"), "Registration successful"))
    print("SUCCESS: Registration complete.")
    return unique_email

def test_login_and_logout(email):
    """Tests user login and logout."""
    print("--- Running: test_login_and_logout ---")
    driver.get(BASE_URL + "login.php")
    wait.until(EC.presence_of_element_located((By.NAME, "email"))).send_keys(email)
    driver.find_element(By.NAME, "password").send_keys("password123")
    # New, corrected line
    driver.find_element(By.CSS_SELECTOR, ".btn-success").click()
    wait.until(EC.url_contains("index.php"))
    print("SUCCESS: Login complete.")
    driver.find_element(By.LINK_TEXT, "Logout").click()
    wait.until(EC.url_contains("login.php"))
    print("SUCCESS: Logout complete.")

if __name__ == "__main__":
    try:
        user_email = test_registration()
        time.sleep(1)
        test_login_and_logout(user_email)
    finally:
        print("\nAll authentication tests finished.")
        driver.quit()