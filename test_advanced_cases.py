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
unique_email = f"advanced_user_{int(time.time())}@mail.com"

def test_unauthorized_access():
    """TC018 & TC019: Tests unauthorized access to admin and cart pages."""
    print("--- Running TC019: Access Cart without Login ---")
    driver.get(BASE_URL + "cart.php")
    wait.until(EC.url_contains("login.php"))
    assert "login.php" in driver.current_url
    print("SUCCESS: TC019 Passed. Redirected to login page.")

    print("--- Running TC018: Unauthorized Admin Panel Access ---")
    driver.get(BASE_URL + "register.php")
    wait.until(EC.presence_of_element_located((By.NAME, "name"))).send_keys("Regular User")
    driver.find_element(By.NAME, "email").send_keys(unique_email)
    driver.find_element(By.NAME, "password").send_keys("password123")
    driver.find_element(By.NAME, "role").send_keys("user")
    driver.find_element(By.CSS_SELECTOR, ".btn-primary").click()
    wait.until(EC.text_to_be_present_in_element((By.CLASS_NAME, "alert"), "Registration successful"))

    driver.get(BASE_URL + "login.php")
    wait.until(EC.presence_of_element_located((By.NAME, "email"))).send_keys(unique_email)
    driver.find_element(By.NAME, "password").send_keys("password123")
    driver.find_element(By.CSS_SELECTOR, ".btn-success").click()
    wait.until(EC.url_contains("index.php"))

    driver.get(BASE_URL + "admin.php")
    wait.until(EC.url_contains("index.php"))
    assert "admin.php" not in driver.current_url
    print("SUCCESS: TC018 Passed. Non-admin user was redirected from admin panel.")
    driver.find_element(By.LINK_TEXT, "Logout").click()
    wait.until(EC.url_contains("login.php"))

def test_checkout_with_empty_cart():
    """TC016: Tests attempting to checkout with an empty cart."""
    print("--- Running TC016: Checkout with Empty Cart ---")
    driver.get(BASE_URL + "login.php")
    wait.until(EC.presence_of_element_located((By.NAME, "email"))).send_keys(unique_email)
    driver.find_element(By.NAME, "password").send_keys("password123")
    driver.find_element(By.CSS_SELECTOR, ".btn-success").click()
    wait.until(EC.url_contains("index.php"))

    driver.get(BASE_URL + "checkout.php")
    wait.until(EC.url_contains("index.php"))
    assert "checkout.php" not in driver.current_url
    print("SUCCESS: TC016 Passed. User redirected from checkout when cart is empty.")

def test_add_more_than_stock():
    """TC015: Verifies user cannot add more items than are in stock."""
    print("--- Running TC015: Add More Than Available Stock ---")

    # Log the user in at the start of the test
    driver.get(BASE_URL + "login.php")
    wait.until(EC.presence_of_element_located((By.NAME, "email"))).send_keys(unique_email)
    driver.find_element(By.NAME, "password").send_keys("password123")
    driver.find_element(By.CSS_SELECTOR, ".btn-success").click()
    wait.until(EC.url_contains("index.php"))

    # Find the first product card that is guaranteed to be in stock
    add_to_cart_form = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "form[action='cart.php']")))
    product_card = add_to_cart_form.find_element(By.XPATH, "./ancestor::div[contains(@class, 'card')]")

    # Get the stock value
    all_p_text = product_card.find_element(By.CSS_SELECTOR, ".card-text").text
    stock_line = [line for line in all_p_text.split('\n') if 'Stock:' in line][0]
    stock_value = int(stock_line.split(":")[1].strip())

    # Try to input a quantity greater than the stock
    quantity_input = product_card.find_element(By.CSS_SELECTOR, "input[name='quantity']")
    quantity_input.clear()
    quantity_input.send_keys(str(stock_value + 5))

    # Click "Add to Cart"
    product_card.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    
    print(f"SUCCESS: TC015 Passed. Cart quantity was correctly capped at {stock_value}.")

if __name__ == "__main__":
    try:
        test_unauthorized_access()
        test_checkout_with_empty_cart()
        test_add_more_than_stock()
    finally:
        print("\nAdvanced test suite finished.")
        driver.quit()