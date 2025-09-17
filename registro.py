import argparse
import random
import string
import sys
import time
from dataclasses import dataclass
from typing import Dict, List, Optional

import pymysql
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.common.exceptions import TimeoutException, WebDriverException

DEFAULT_BASE_URL = "http://localhost:8080/Biblioteca-Arco-Iris-Manual/HTML/registro.php"
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'biblioteca_arco_iris',
    'charset': 'utf8mb4'
}

@dataclass
class TestCase:
    name: str
    data: Dict[str, str]
    expect_success: bool
    note: Optional[str] = None

def generate_unique_email(prefix: str = "teste") -> str:
    timestamp = int(time.time() * 1000)
    rand = "".join(random.choices(string.ascii_lowercase + string.digits, k=5))
    return f"{prefix}.{timestamp}.{rand}@example.com"

def generate_unique_cpf() -> str:
    """Gera CPF único simples (não necessariamente válido)"""
    timestamp = int(time.time() * 1000)
    return f"{(timestamp//1000000)%1000:03d}.{(timestamp//1000)%1000:03d}.{timestamp%1000:03d}-00"

def generate_test_data() -> Dict[str, str]:
    return {
        "nome": "Usuário Teste Selenium",
        "cpf": generate_unique_cpf(),
        "telefone": "(11) 90000-0000",
        "email": generate_unique_email(),
        "senha": "SenhaF0rte123!",
    }

def setup_chrome_driver(headless: bool = False) -> webdriver.Chrome:
    chrome_options = Options()
    chrome_options.add_argument("--no-sandbox")
    chrome_options.add_argument("--disable-dev-shm-usage")
    chrome_options.add_argument("--disable-gpu")
    chrome_options.add_argument("--window-size=1920,1080")
    if headless:
        chrome_options.add_argument("--headless")
    try:
        driver = webdriver.Chrome(options=chrome_options)
        return driver
    except WebDriverException as e:
        print(f"Erro ao configurar ChromeDriver: {e}")
        print("Certifique-se de que o ChromeDriver está instalado e no PATH")
        sys.exit(1)

def check_user_in_database(email: str) -> bool:
    """Verifica se o usuário foi inserido no banco de dados"""
    try:
        connection = pymysql.connect(**DB_CONFIG)
        cursor = connection.cursor()
        query = "SELECT id, nome, email, cpf, telefone FROM usuarios WHERE email = %s"
        cursor.execute(query, (email,))
        result = cursor.fetchone()
        cursor.close()
        connection.close()
        return result is not None
    except Exception as e:
        print(f"Erro ao verificar banco de dados: {e}")
        return False

def run_registration_test(driver: webdriver.Chrome, url: str, test_data: Dict[str, str]) -> bool:
    """Executa o teste de registro usando Selenium"""
    try:
        driver.get(url)
        wait = WebDriverWait(driver, 10)

        # Preenche formulário
        driver.find_element(By.NAME, "nome").send_keys(test_data["nome"])
        driver.find_element(By.NAME, "cpf").send_keys(test_data["cpf"])
        driver.find_element(By.NAME, "telefone").send_keys(test_data["telefone"])
        driver.find_element(By.NAME, "email").send_keys(test_data["email"])
        driver.find_element(By.NAME, "senha").send_keys(test_data["senha"])

        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()

        # Aguarda mensagem de sucesso na página (div com id='success-message')
        try:
            success_msg = wait.until(EC.presence_of_element_located((By.ID, "success-message")))
            print(f"✓ Registro bem-sucedido: {success_msg.text}")
            return True
        except TimeoutException:
            print("✗ Registro falhou (não encontrou mensagem de sucesso)")
            return False

    except Exception as e:
        print(f"✗ Erro durante o teste: {e}")
        return False

def build_test_suite() -> List[TestCase]:
    return [
        TestCase(
            name="Registro válido com dados completos",
            data=generate_test_data(),
            expect_success=True,
        ),
        TestCase(
            name="Registro com nome vazio",
            data={**generate_test_data(), "nome": ""},
            expect_success=False,
            note="Deve falhar por validação do campo required"
        ),
        TestCase(
            name="Registro com email inválido",
            data={**generate_test_data(), "email": "email-invalido"},
            expect_success=False,
            note="Deve falhar por validação do type=email"
        ),
        TestCase(
            name="Registro com senha curta",
            data={**generate_test_data(), "senha": "123"},
            expect_success=False,
            note="Deve falhar por validação do minlength"
        ),
    ]

def main() -> int:
    parser = argparse.ArgumentParser(description="Teste de registro de usuários usando Selenium")
    parser.add_argument("--url", default=DEFAULT_BASE_URL, help="URL da página de registro")
    parser.add_argument("--headless", action="store_true", help="Executar em modo headless")
    args = parser.parse_args()

    driver = setup_chrome_driver(args.headless)
    results = []

    try:
        for i, test_case in enumerate(build_test_suite(), 1):
            print(f"\n--- Teste {i}: {test_case.name} ---")
            if test_case.note:
                print(f"Nota: {test_case.note}")

            registration_success = run_registration_test(driver, args.url, test_case.data)

            db_check = False
            if registration_success and test_case.expect_success:
                print("Verificando se o usuário foi inserido no banco de dados...")
                db_check = check_user_in_database(test_case.data["email"])
                if db_check:
                    print(f"✓ Usuário encontrado no banco: {test_case.data['email']}")
                else:
                    print(f"✗ Usuário NÃO encontrado no banco: {test_case.data['email']}")

            if test_case.expect_success:
                test_passed = registration_success and db_check
            else:
                test_passed = not registration_success

            status = "PASS" if test_passed else "FAIL"
            print(f"[{status}] {test_case.name}")
            results.append(test_passed)
            time.sleep(1)
    finally:
        driver.quit()

    total = len(results)
    passed = sum(1 for r in results if r)
    print(f"\n{'='*50}\nRESUMO DOS TESTES:")
    print(f"Total: {total}, Passou: {passed}, Falhou: {total - passed}, Taxa de sucesso: {(passed/total)*100:.1f}%")

    return 0 if passed == total else 1

if __name__ == "__main__":
    sys.exit(main())
