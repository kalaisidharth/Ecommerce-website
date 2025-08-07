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

def test_admin_stock_update():
    """Tests the admin stock update functionality."""
    print("--- Running: test_admin_stock_update ---")
    driver.get(BASE_URL + "login.php")
    wait.until(EC.presence_of_element_located((By.NAME, "email"))).send_keys("admin@example.com")
    driver.find_element(By.NAME, "password").send_keys("adminpass")
    
    # This is the corrected line for the login button
    driver.find_element(By.CSS_SELECTOR, ".btn-success").click()

    wait.until(EC.url_contains("index.php"))
    print("SUCCESS: Admin login complete.")
    driver.find_element(By.LINK_TEXT, "Admin Panel").click()

    wait.until(EC.url_contains("admin.php"))
    print("Navigated to Admin Panel.")
    stock_input = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "input[name*='stock']")))
    stock_input.clear()
    stock_input.send_keys("99")
    
    # This selector was already correct and does not need to be changed
    driver.find_element(By.CSS_SELECTOR, "button[name='update_stock']").click()
    
    wait.until(EC.text_to_be_present_in_element((By.CLASS_NAME, "alert"), "Stock updated successfully!"))
    print("SUCCESS: Stock updated.")

if __name__ == "__main__":
    try:
        test_admin_stock_update()
    finally:
        print("\nAdmin test finished.")
        driver.quit()