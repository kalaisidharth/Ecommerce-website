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
# Assumes the user from the first test suite exists
user_email = "john_registered@mail.com" # Use a known registered user email

# Pre-Test: Register a user if not present for isolated testing
def register_user_for_test():
    driver.get(BASE_URL + "register.php")
    wait.until(EC.presence_of_element_located((By.NAME, "name"))).send_keys("Order User")
    driver.find_element(By.NAME, "email").send_keys(user_email)
    driver.find_element(By.NAME, "password").send_keys("123456")
    driver.find_element(By.NAME, "role").send_keys("user")
    driver.find_element(By.CSS_SELECTOR, ".btn-primary").click()

def test_add_to_cart_and_update_quantity():
    """TC010 & TC011: Adds product and increases quantity."""
    print("--- Running TC010 & TC011: Add to Cart & Update Quantity ---")
    driver.get(BASE_URL + "login.php")
    wait.until(EC.presence_of_element_located((By.NAME, "email"))).send_keys(user_email)
    driver.find_element(By.NAME, "password").send_keys("123456")
    driver.find_element(By.CSS_SELECTOR, ".btn-success").click()
    
    # Add to cart
    wait.until(EC.element_to_be_clickable((By.CSS_SELECTOR, "form[action='cart.php'] button"))).click()
    wait.until(EC.text_to_be_present_in_element((By.CLASS_NAME, "alert"), "Item added to cart!"))
    print("SUCCESS: TC010 Product added to cart.")

    # Go to cart and update quantity
    driver.find_element(By.LINK_TEXT, "🛒 View Cart").click()
    quantity_input = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "input[name*='quantities']")))
    quantity_input.clear()
    quantity_input.send_keys("2")
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    wait.until(EC.text_to_be_present_in_element((By.CLASS_NAME, "alert"), "Cart updated."))
    print("SUCCESS: TC011 Product quantity updated.")

def test_place_order():
    """TC013: Places an order."""
    print("--- Running TC013: Place Order ---")
    driver.find_element(By.LINK_TEXT, "Proceed to Checkout").click()
    wait.until(EC.url_contains("checkout.php"))
    wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "input[placeholder='Full Name']"))).send_keys("Order User")
    driver.find_element(By.CSS_SELECTOR, "input[placeholder='Address']").send_keys("123 Test St")
    driver.find_element(By.CSS_SELECTOR, "input[placeholder='City']").send_keys("Testville")
    driver.find_element(By.CSS_SELECTOR, "input[placeholder='Zip Code']").send_keys("12345")
    driver.find_element(By.CSS_SELECTOR, "input[placeholder='Email']").send_keys(user_email)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    wait.until(EC.url_contains("thankyou.php"))
    assert "Thank You!" in driver.page_source
    print("SUCCESS: TC013 Order placed successfully.")

if __name__ == "__main__":
    try:
        # This setup function ensures the user exists before testing
        register_user_for_test() 
        test_add_to_cart_and_update_quantity()
        test_place_order()
    finally:
        print("\nOrder process test suite finished.")
        driver.quit()