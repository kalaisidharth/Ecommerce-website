import time
import os
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

BASE_URL = "http://localhost/ecommerce/"
ADMIN_EMAIL = "admin@example.com"  # Use a real admin email
ADMIN_PASS = "adminpass"           # Use the correct admin password
TEST_IMAGE_PATH = r"C:\xampp\htdocs\ecommerce\images\test.jpg"  # Ensure this file exists

driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()))
wait = WebDriverWait(driver, 10)

def login_as_admin():
    driver.get(BASE_URL + "login.php")
    wait.until(EC.presence_of_element_located((By.NAME, "email"))).send_keys(ADMIN_EMAIL)
    driver.find_element(By.NAME, "password").send_keys(ADMIN_PASS)
    driver.find_element(By.CSS_SELECTOR, ".btn-success").click()
    # Wait for either dashboard or error
    try:
        wait.until(EC.url_contains("admin_dashboard.php"))
        print("Admin login: Success")
    except:
        print("Login failed or admin not redirected. Check credentials and login.php logic.")
        driver.quit()
        exit()

def test_add_product():
    print("Testing: Add Product")
    driver.get(BASE_URL + "admin_dashboard.php")
    if not os.path.exists(TEST_IMAGE_PATH):
        print(f"Image not found: {TEST_IMAGE_PATH}")
        driver.quit()
        exit()
    wait.until(EC.presence_of_element_located((By.NAME, "name"))).send_keys("Test Product " + str(int(time.time())))
    driver.find_element(By.NAME, "brand").send_keys("TestBrand")
    driver.find_element(By.NAME, "price").send_keys("99.99")
    driver.find_element(By.NAME, "stock").send_keys("10")
    driver.find_element(By.NAME, "rating").send_keys("5")
    driver.find_element(By.NAME, "image").send_keys(TEST_IMAGE_PATH)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    wait.until(EC.presence_of_element_located((By.CLASS_NAME, "alert-success")))
    print("Add Product: Success")

def test_update_stock():
    print("Testing: Update Stock")
    driver.get(BASE_URL + "admin_dashboard.php")
    update_forms = driver.find_elements(By.CSS_SELECTOR, "form.d-flex")
    if update_forms:
        update_forms[0].find_element(By.NAME, "new_stock").clear()
        update_forms[0].find_element(By.NAME, "new_stock").send_keys("20")
        update_forms[0].find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        wait.until(EC.presence_of_element_located((By.CLASS_NAME, "alert-success")))
        print("Update Stock: Success")
    else:
        print("No product found to update stock.")



if __name__ == "__main__":
    try:
        login_as_admin()
        test_add_product()
        test_update_stock()
        print("All tests passed successfully.")
    finally:
        print("Admin dashboard tests finished.")
        driver.quit()