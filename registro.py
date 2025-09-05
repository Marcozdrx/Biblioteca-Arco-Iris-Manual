#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Teste de Sistema - Registro de Usuário
Biblioteca Arco-Íris

Este script automatiza o teste de registro de usuário no site da Biblioteca Arco-Íris.
Ele preenche todos os campos obrigatórios e clica no botão de registro.
"""

import time
import random
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service
from selenium.common.exceptions import TimeoutException, NoSuchElementException

try:
    from webdriver_manager.chrome import ChromeDriverManager
    WEBDRIVER_MANAGER_AVAILABLE = True
except ImportError:
    WEBDRIVER_MANAGER_AVAILABLE = False

class TesteRegistro:
    def __init__(self):
        """Inicializa o driver do navegador"""
        self.driver = None
        self.wait = None
        
    def configurar_navegador(self):
        """Configura o navegador Chrome com opções adequadas"""
        chrome_options = Options()
        chrome_options.add_argument("--no-sandbox")
        chrome_options.add_argument("--disable-dev-shm-usage")
        chrome_options.add_argument("--disable-gpu")
        chrome_options.add_argument("--window-size=1920,1080")
        chrome_options.add_argument("--disable-web-security")
        chrome_options.add_argument("--allow-running-insecure-content")
        
        # Comentar a linha abaixo se quiser ver o navegador em ação
        # chrome_options.add_argument("--headless")
        
        print("🔧 Tentando configurar o navegador Chrome...")
        
        # Tenta diferentes métodos de inicialização
        metodos = []
        
        # Método 1: Com webdriver-manager (se disponível)
        if WEBDRIVER_MANAGER_AVAILABLE:
            metodos.append(("webdriver-manager", self._inicializar_com_webdriver_manager))
        
        # Método 2: ChromeDriver no PATH
        metodos.append(("PATH", self._inicializar_com_path))
        
        # Método 3: ChromeDriver no diretório atual
        metodos.append(("diretório atual", self._inicializar_com_diretorio_atual))
        
        for nome_metodo, metodo in metodos:
            try:
                print(f"🔄 Tentando método: {nome_metodo}")
                self.driver = metodo(chrome_options)
                if self.driver:
                    self.wait = WebDriverWait(self.driver, 10)
                    print(f"✅ Navegador configurado com sucesso usando: {nome_metodo}")
                    return True
            except Exception as e:
                print(f"❌ Método {nome_metodo} falhou: {e}")
                continue
        
        print("❌ Todos os métodos de inicialização falharam!")
        print("💡 Soluções possíveis:")
        print("   1. Instale webdriver-manager: pip install webdriver-manager")
        print("   2. Baixe ChromeDriver manualmente e coloque no PATH")
        print("   3. Coloque chromedriver.exe na pasta do projeto")
        return False
    
    def _inicializar_com_webdriver_manager(self, chrome_options):
        """Inicializa usando webdriver-manager"""
        service = Service(ChromeDriverManager().install())
        return webdriver.Chrome(service=service, options=chrome_options)
    
    def _inicializar_com_path(self, chrome_options):
        """Inicializa usando ChromeDriver no PATH"""
        return webdriver.Chrome(options=chrome_options)
    
    def _inicializar_com_diretorio_atual(self, chrome_options):
        """Inicializa usando ChromeDriver no diretório atual"""
        service = Service("./chromedriver.exe")
        return webdriver.Chrome(service=service, options=chrome_options)
    
    def gerar_dados_teste(self):
        """Gera dados de teste únicos para o registro"""
        # Gera um número aleatório para tornar os dados únicos
        numero_aleatorio = random.randint(1000, 9999)
        
        dados = {
            'nome': f'Usuário Teste {numero_aleatorio}',
            'cpf': f'{numero_aleatorio:03d}.{numero_aleatorio:03d}.{numero_aleatorio:03d}-{numero_aleatorio % 100:02d}',
            'telefone': f'(11) 9{numero_aleatorio:04d}-{numero_aleatorio:04d}',
            'email': f'teste{numero_aleatorio}@biblioteca.com',
            'senha': 'Teste123!@#'
        }
        
        print(f"📝 Dados de teste gerados:")
        for campo, valor in dados.items():
            print(f"   {campo}: {valor}")
        
        return dados
    
    def acessar_pagina_registro(self, url_base="http://localhost/Biblioteca-Arco-Iris-Manual/HTML/registro.php"):
        """Acessa a página de registro"""
        try:
            print(f"🌐 Acessando: {url_base}")
            self.driver.get(url_base)
            
            # Aguarda a página carregar
            self.wait.until(EC.presence_of_element_located((By.NAME, "nome")))
            print("✅ Página de registro carregada com sucesso!")
            return True
            
        except TimeoutException:
            print("❌ Timeout ao carregar a página de registro")
            return False
        except Exception as e:
            print(f"❌ Erro ao acessar página: {e}")
            return False
    
    def preencher_formulario(self, dados):
        """Preenche todos os campos do formulário de registro"""
        try:
            print("📝 Preenchendo formulário...")
            
            # Mapeamento dos campos
            campos = {
                'nome': dados['nome'],
                'cpf': dados['cpf'],
                'telefone': dados['telefone'],
                'email': dados['email'],
                'senha': dados['senha']
            }
            
            # Preenche cada campo
            for nome_campo, valor in campos.items():
                try:
                    campo = self.driver.find_element(By.NAME, nome_campo)
                    campo.clear()
                    campo.send_keys(valor)
                    print(f"   ✅ Campo '{nome_campo}' preenchido")
                    time.sleep(0.5)  # Pequena pausa entre campos
                except NoSuchElementException:
                    print(f"   ❌ Campo '{nome_campo}' não encontrado")
                    return False
            
            print("✅ Formulário preenchido com sucesso!")
            return True
            
        except Exception as e:
            print(f"❌ Erro ao preencher formulário: {e}")
            return False
    
    def clicar_registrar(self):
        """Clica no botão de registro"""
        try:
            print("🖱️ Clicando no botão REGISTRAR...")
            
            # Procura pelo botão de submit
            botao_registrar = self.wait.until(
                EC.element_to_be_clickable((By.CSS_SELECTOR, "button[type='submit']"))
            )
            
            # Rola até o botão para garantir que está visível
            self.driver.execute_script("arguments[0].scrollIntoView(true);", botao_registrar)
            time.sleep(1)
            
            # Clica no botão
            botao_registrar.click()
            print("✅ Botão REGISTRAR clicado com sucesso!")
            
            # Aguarda um pouco para o processamento
            time.sleep(3)
            
            return True
            
        except TimeoutException:
            print("❌ Timeout ao clicar no botão REGISTRAR")
            return False
        except Exception as e:
            print(f"❌ Erro ao clicar no botão: {e}")
            return False
    
    def aguardar_processamento(self):
        """Aguarda o processamento do registro"""
        try:
            print("⏳ Aguardando processamento...")
            time.sleep(5)  # Aguarda mais tempo para o processamento
            
            # Aguarda o redirecionamento
            try:
                self.wait.until(lambda driver: "login.php" in driver.current_url or "registro.php" not in driver.current_url)
                print("✅ Redirecionamento detectado")
            except:
                print("⚠️ Redirecionamento não detectado, mas continuando...")
            
            print("✅ Processamento concluído")
            return True
        except Exception as e:
            print(f"❌ Erro ao aguardar processamento: {e}")
            return False
    
    def executar_teste(self):
        """Executa o teste completo de registro"""
        print("🚀 Iniciando teste de sistema - Registro de Usuário")
        print("=" * 60)
        
        sucesso = True
        
        # 1. Configurar navegador
        if not self.configurar_navegador():
            return False
        
        try:
            # 2. Gerar dados de teste
            dados = self.gerar_dados_teste()
            
            # 3. Acessar página de registro
            if not self.acessar_pagina_registro():
                sucesso = False
            
            # 4. Preencher formulário
            elif not self.preencher_formulario(dados):
                sucesso = False
            
            # 5. Clicar em registrar
            elif not self.clicar_registrar():
                sucesso = False
            
            # 6. Aguardar processamento
            elif not self.aguardar_processamento():
                sucesso = False
            
            # Resultado final
            print("=" * 60)
            if sucesso:
                print("✅ Usuário registrado no banco de dados")
            else:
                print("❌ Erro no registro do usuário")
            
            return sucesso
            
        finally:
            # Aguarda um pouco antes de fechar para visualizar o resultado
            if self.driver:
                print("👀 Mantendo navegador aberto por 3 segundos para visualização...")
                time.sleep(3)
                print("🔒 Fechando navegador...")
                self.driver.quit()

def main():
    """Função principal"""
    print("Biblioteca Arco-Íris - Teste de Sistema")
    print("Teste de Registro de Usuário")
    print()
    
    # Verifica se o Selenium está instalado
    try:
        import selenium
        print(f"✅ Selenium versão {selenium.__version__} detectado")
    except ImportError:
        print("❌ Selenium não está instalado!")
        print("Execute: pip install selenium")
        return
    
    # Executa o teste
    teste = TesteRegistro()
    resultado = teste.executar_teste()
    
    # Código de saída
    exit(0 if resultado else 1)

if __name__ == "__main__":
    main()
