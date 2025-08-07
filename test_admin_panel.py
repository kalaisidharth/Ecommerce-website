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

def test_admin_login():
    """TC005: Tests successful admin login."""
    print("--- Running TC005: Admin Login ---")
    driver.get(BASE_URL + "login.php")
    wait.until(EC.presence_of_element_located((By.NAME, "email"))).send_keys("admin@example.com")
    driver.find_element(By.NAME, "password").send_keys("adminpass")
    driver.find_element(By.CSS_SELECTOR, ".btn-success").click()
    wait.until(EC.presence_of_element_located((By.LINK_TEXT, "Admin Panel")))
    print("SUCCESS: TC005 Passed.")

def test_manage_and_set_out_of_stock():
    """TC008 & TC012: Views product list and marks an item as out of stock."""
    print("--- Running TC008 & TC012: Manage Products and Out of Stock ---")
    driver.find_element(By.LINK_TEXT, "Admin Panel").click()
    wait.until(EC.url_contains("admin.php"))
    print("SUCCESS: TC008 Navigated to Manage Products.")
    
    # Mark first product as out of stock
    stock_input = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "input[name*='stock']")))
    stock_input.clear()
    stock_input.send_keys("0")
    driver.find_element(By.CSS_SELECTOR, "button[name='update_stock']").click()
    wait.until(EC.text_to_be_present_in_element((By.CLASS_NAME, "alert"), "Stock updated successfully!"))
    print("SUCCESS: TC012 Marked product as Out of Stock.")
    
    # Go back to shop to verify
    driver.find_element(By.LINK_TEXT, "Back to Shop").click()
    wait.until(EC.url_contains("index.php"))
    out_of_stock_badge = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, ".badge-oos")))
    assert out_of_stock_badge.text == "Out of Stock"
    print("SUCCESS: Verified 'Out of Stock' label is visible.")

if __name__ == "__main__":
    try:
        test_admin_login()
        test_manage_and_set_out_of_stock()
    finally:
        print("\nAdmin panel test suite finished.")
        driver.quit()