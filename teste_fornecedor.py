#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Teste de Sistema - Cadastro de Fornecedor
Biblioteca Arco-Íris

Este script automatiza o teste de cadastro de fornecedor no site da Biblioteca Arco-Íris.
Ele preenche todos os campos obrigatórios e clica no botão de salvar.
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

class TesteFornecedor:
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
    
    def gerar_dados_fornecedor(self):
        """Gera dados de teste únicos para o fornecedor"""
        numero_aleatorio = random.randint(1000, 9999)
        
        # Lista de nomes de empresas fictícias
        nomes_empresas = [
            "Editora", "Livraria", "Distribuidora", "Comércio", "Importadora"
        ]
        sufixos = [
            "Literária", "Cultural", "Educacional", "Técnica", "Científica", 
            "Artes", "Conhecimento", "Saber", "Cultura", "Livros"
        ]
        
        nome_empresa = f"{random.choice(nomes_empresas)} {random.choice(sufixos)} {numero_aleatorio}"
        
        # CEPs válidos do Brasil (São Paulo)
        ceps_validos = [
            "01310-100", "04038-001", "05407-002", "01234-567", "04567-890",
            "02345-678", "05678-901", "03456-789", "06789-012", "04567-890"
        ]
        
        cep_escolhido = random.choice(ceps_validos)
        
        dados = {
            'nome': nome_empresa,
            'cpfCnpj': f'{numero_aleatorio:02d}.{numero_aleatorio:03d}.{numero_aleatorio:03d}/0001-{numero_aleatorio % 100:02d}',  # CNPJ
            'telefone': f'(11) {numero_aleatorio:04d}-{numero_aleatorio:04d}',
            'email': f'contato@{nome_empresa.lower().replace(" ", "")}.com.br',
            'cep': cep_escolhido,
            'cidade': 'São Paulo',
            'estado': 'SP'
        }
        
        print(f"📝 Dados do fornecedor gerados:")
        for campo, valor in dados.items():
            print(f"   {campo}: {valor}")
        
        return dados
    
    def acessar_pagina_fornecedor(self, url_base="http://localhost/Biblioteca-Arco-Iris-Manual/HTML/cadastrar-fornecedor-publico.php"):
        """Acessa a página de cadastro de fornecedor"""
        try:
            print(f"🌐 Acessando: {url_base}")
            self.driver.get(url_base)
            
            # Aguarda a página carregar
            self.wait.until(EC.presence_of_element_located((By.ID, "nome")))
            print("✅ Página de cadastro de fornecedor carregada com sucesso!")
            return True
            
        except TimeoutException:
            print("❌ Timeout ao carregar a página de cadastro de fornecedor")
            print("⚠️ Verifique se você está logado como administrador")
            return False
        except Exception as e:
            print(f"❌ Erro ao acessar página: {e}")
            return False
    
    def preencher_formulario_fornecedor(self, dados):
        """Preenche todos os campos do formulário de fornecedor"""
        try:
            print("📝 Preenchendo formulário de fornecedor...")
            
            # Mapeamento dos campos
            campos = {
                'nome': dados['nome'],
                'cpfCnpj': dados['cpfCnpj'],
                'telefone': dados['telefone'],
                'email': dados['email'],
                'cep': dados['cep'],
                'cidade': dados['cidade'],
                'estado': dados['estado']
            }
            
            # Preenche cada campo
            for nome_campo, valor in campos.items():
                try:
                    campo = self.driver.find_element(By.ID, nome_campo)
                    campo.clear()
                    campo.send_keys(valor)
                    print(f"   ✅ Campo '{nome_campo}' preenchido")
                    time.sleep(0.5)  # Pequena pausa entre campos
                    
                    # Aguarda um pouco após preencher o CEP para a busca automática
                    if nome_campo == 'cep':
                        time.sleep(3)  # Aguarda mais tempo para a busca automática
                        
                        # Sempre preenche manualmente cidade e estado após o CEP
                        try:
                            cidade_campo = self.driver.find_element(By.ID, "cidade")
                            cidade_campo.clear()
                            cidade_campo.send_keys(dados['cidade'])
                            print("   ✅ Campo 'cidade' preenchido")
                            
                            estado_campo = self.driver.find_element(By.ID, "estado")
                            estado_campo.clear()
                            estado_campo.send_keys(dados['estado'])
                            print("   ✅ Campo 'estado' preenchido")
                        except Exception as e:
                            print(f"   ⚠️ Erro ao preencher cidade/estado: {e}")
                        
                except NoSuchElementException:
                    print(f"   ❌ Campo '{nome_campo}' não encontrado")
                    return False
            
            # Verifica se todos os campos obrigatórios foram preenchidos
            campos_obrigatorios = ['nome', 'cpfCnpj', 'telefone', 'email', 'cep', 'cidade', 'estado']
            for campo in campos_obrigatorios:
                try:
                    elemento = self.driver.find_element(By.ID, campo)
                    if not elemento.get_attribute("value").strip():
                        print(f"   ⚠️ Campo '{campo}' está vazio, preenchendo novamente...")
                        elemento.clear()
                        elemento.send_keys(dados[campo])
                        time.sleep(0.5)
                except:
                    print(f"   ❌ Erro ao verificar campo '{campo}'")
            
            print("✅ Formulário de fornecedor preenchido com sucesso!")
            return True
            
        except Exception as e:
            print(f"❌ Erro ao preencher formulário: {e}")
            return False
    
    def clicar_salvar(self):
        """Clica no botão de salvar"""
        try:
            print("🖱️ Clicando no botão SALVAR...")
            
            # Procura pelo botão de submit
            botao_salvar = self.wait.until(
                EC.element_to_be_clickable((By.CSS_SELECTOR, "button[type='submit']"))
            )
            
            # Rola até o botão para garantir que está visível
            self.driver.execute_script("arguments[0].scrollIntoView(true);", botao_salvar)
            time.sleep(1)
            
            # Clica no botão
            botao_salvar.click()
            print("✅ Botão SALVAR clicado com sucesso!")
            
            # Aguarda um pouco para o processamento
            time.sleep(3)
            
            return True
            
        except TimeoutException:
            print("❌ Timeout ao clicar no botão SALVAR")
            return False
        except Exception as e:
            print(f"❌ Erro ao clicar no botão: {e}")
            return False
    
    def aguardar_processamento(self):
        """Aguarda o processamento do cadastro"""
        try:
            print("⏳ Aguardando processamento...")
            time.sleep(7)  # Aguarda mais tempo para o processamento
            
            # Aguarda o redirecionamento ou mudança na página
            try:
                self.wait.until(lambda driver: "sucesso-fornecedor.php" in driver.current_url or "cadastrar-fornecedor-publico.php" not in driver.current_url)
                print("✅ Redirecionamento detectado")
            except:
                print("⚠️ Redirecionamento não detectado, mas continuando...")
            
            # Verifica se há mensagens de erro na página
            try:
                mensagens_erro = self.driver.find_elements(By.CSS_SELECTOR, ".error, .alert-danger, .text-danger")
                if mensagens_erro:
                    for msg in mensagens_erro:
                        if msg.is_displayed():
                            print(f"⚠️ Mensagem de erro encontrada: {msg.text}")
            except:
                pass
            
            print("✅ Processamento concluído")
            return True
        except Exception as e:
            print(f"❌ Erro ao aguardar processamento: {e}")
            return False
    
    def executar_teste(self):
        """Executa o teste completo de cadastro de fornecedor"""
        print("🚀 Iniciando teste de sistema - Cadastro de Fornecedor")
        print("=" * 60)
        
        sucesso = True
        
        # 1. Configurar navegador
        if not self.configurar_navegador():
            return False
        
        try:
            # 2. Gerar dados de teste
            dados = self.gerar_dados_fornecedor()
            
            # 3. Acessar página de cadastro
            if not self.acessar_pagina_fornecedor():
                sucesso = False
            
            # 4. Preencher formulário
            elif not self.preencher_formulario_fornecedor(dados):
                sucesso = False
            
            # 5. Clicar em salvar
            elif not self.clicar_salvar():
                sucesso = False
            
            # 6. Aguardar processamento
            elif not self.aguardar_processamento():
                sucesso = False
            
            # Resultado final
            print("=" * 60)
            if sucesso:
                print("✅ Fornecedor registrado no banco de dados")
            else:
                print("❌ Erro no registro do fornecedor")
            
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
    print("Teste de Cadastro de Fornecedor")
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
    teste = TesteFornecedor()
    resultado = teste.executar_teste()
    
    # Código de saída
    exit(0 if resultado else 1)

if __name__ == "__main__":
    main()
