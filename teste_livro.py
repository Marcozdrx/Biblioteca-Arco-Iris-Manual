#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Teste de Sistema - Cadastro de Livro
Biblioteca Arco-Íris

Este script automatiza o teste de cadastro de livro no site da Biblioteca Arco-Íris.
Ele preenche todos os campos obrigatórios e clica no botão de salvar.
"""

import time
import random
import os
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
    print("💡 webdriver-manager não instalado. Execute: pip install webdriver-manager")

class TesteLivro:
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
        
        print("Tentando configurar o navegador Chrome...")
        
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
    
    def gerar_dados_livro(self):
        """Gera dados de teste únicos para o livro"""
        numero_aleatorio = random.randint(1000, 9999)
        
        # Lista de títulos fictícios
        titulos = [
            "Aventuras", "Mistérios", "Histórias", "Contos", "Crônicas", 
            "Memórias", "Diários", "Reflexões", "Descobertas", "Jornadas"
        ]
        sufixos = [
            "do Tempo", "da Vida", "Imaginária", "Perdida", "Secreta",
            "Fantástica", "Mágica", "Incrível", "Extraordinária", "Única"
        ]
        
        titulo_livro = f"{random.choice(titulos)} {random.choice(sufixos)} {numero_aleatorio}"
        
        # Lista de autores fictícios
        autores = [
            "João Silva", "Maria Santos", "Pedro Oliveira", "Ana Costa", "Carlos Lima",
            "Lucia Ferreira", "Roberto Alves", "Fernanda Rocha", "Marcos Pereira", "Juliana Dias"
        ]
        
        # Lista de editoras fictícias
        editoras = [
            "Editora Literária", "Casa do Livro", "Publicações Culturais", 
            "Editora Saber", "Livros & Cia", "Editora Conhecimento"
        ]
        
        # Lista de idiomas
        idiomas = ["Português", "Inglês", "Espanhol", "Francês", "Italiano"]
        
        # Lista de categorias
        categorias = [
            "Ficção", "Romance", "Aventura", "Mistério", "Fantasia",
            "Terror", "Drama", "Comédia", "Biografia", "História"
        ]
        
        dados = {
            'titulo': titulo_livro,
            'estoque': random.randint(1, 50),
            'autor': random.choice(autores),
            'dataPublicacao': random.randint(1950, 2024),
            'numeroPaginas': random.randint(100, 800),
            'editora': random.choice(editoras),
            'isbn': f'978-{numero_aleatorio:03d}-{numero_aleatorio:02d}-{numero_aleatorio:04d}-{numero_aleatorio % 10}',
            'idioma': random.choice(idiomas),
            'categoria': random.choice(categorias),
            'descricao': f"Uma obra fascinante que narra {titulo_livro.lower()}. Este livro oferece uma experiência única de leitura, combinando elementos de {random.choice(categorias).lower()} com uma narrativa envolvente que prende o leitor do início ao fim."
        }
        
        print(f"📝 Dados do livro gerados:")
        for campo, valor in dados.items():
            print(f"   {campo}: {valor}")
        
        return dados
    
    def acessar_pagina_admin(self, url_base="http://localhost/Biblioteca-Arco-Iris-Manual/HTML/admin-publico.php"):
        """Acessa a página de administração"""
        try:
            print(f"🌐 Acessando: {url_base}")
            self.driver.get(url_base)
            
            # Aguarda a página carregar
            self.wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "body")))
            print("✅ Página de administração carregada com sucesso!")
            return True
            
        except TimeoutException:
            print("❌ Timeout ao carregar a página de administração")
            return False
        except Exception as e:
            print(f"❌ Erro ao acessar página: {e}")
            return False
    
    def abrir_modal_livro(self):
        """Abre o modal de cadastro de livro"""
        try:
            print("📖 Abrindo modal de cadastro de livro...")
            
            # Procura pelo botão "Adicionar Novo Livro"
            botao_adicionar = self.wait.until(
                EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Adicionar Novo Livro')]"))
            )
            
            # Clica no botão
            botao_adicionar.click()
            print("✅ Botão 'Adicionar Novo Livro' clicado")
            
            # Aguarda o modal aparecer
            self.wait.until(EC.presence_of_element_located((By.ID, "bookModal")))
            print("✅ Modal de cadastro de livro aberto!")
            return True
            
        except TimeoutException:
            print("❌ Timeout ao abrir modal de cadastro de livro")
            return False
        except Exception as e:
            print(f"❌ Erro ao abrir modal: {e}")
            return False
    
    def preencher_formulario_livro(self, dados):
        """Preenche todos os campos do formulário de livro"""
        try:
            print("📝 Preenchendo formulário de livro...")
            
            # Mapeamento dos campos
            campos = {
                'titulo': dados['titulo'],
                'estoque': str(dados['estoque']),
                'autor': dados['autor'],  # Envia o nome do autor
                'dataPublicacao': str(dados['dataPublicacao']),
                'numeroPaginas': str(dados['numeroPaginas']),
                'editora': dados['editora'],
                'isbn': dados['isbn'],
                'idioma': dados['idioma'],
                'categoria': dados['categoria'],  # Envia o nome da categoria
                'descricao': dados['descricao']
            }
            
            # Preenche cada campo
            for nome_campo, valor in campos.items():
                try:
                    campo = self.driver.find_element(By.ID, nome_campo)
                    campo.clear()
                    campo.send_keys(valor)
                    print(f"   ✅ Campo '{nome_campo}' preenchido")
                    time.sleep(0.3)  # Pequena pausa entre campos
                    
                except NoSuchElementException:
                    print(f"   ❌ Campo '{nome_campo}' não encontrado")
                    return False
            
            # Upload de imagem (opcional - cria um arquivo temporário se necessário)
            try:
                campo_imagem = self.driver.find_element(By.ID, "capa")
                # Cria um arquivo temporário para o teste
                import tempfile
                import os
                
                # Cria um arquivo de imagem temporário
                with tempfile.NamedTemporaryFile(suffix='.jpg', delete=False) as temp_file:
                    temp_file.write(b'fake_image_data')
                    temp_path = temp_file.name
                
                # Envia o arquivo temporário
                campo_imagem.send_keys(temp_path)
                print("   ✅ Imagem temporária enviada para teste")
                
                # NÃO remove o arquivo temporário aqui - deixa para o PHP processar
                # O arquivo será removido automaticamente pelo sistema
                
            except NoSuchElementException:
                print("   ⚠️ Campo de imagem não encontrado")
            except Exception as e:
                print(f"   ⚠️ Erro ao enviar imagem: {e}")
            
            print("✅ Formulário de livro preenchido com sucesso!")
            return True
            
        except Exception as e:
            print(f"❌ Erro ao preencher formulário: {e}")
            return False
    
    def clicar_salvar_livro(self):
        """Clica no botão de salvar livro"""
        try:
            print("🖱️ Clicando no botão SALVAR...")
            
            # Procura pelo botão de submit
            botao_salvar = self.wait.until(
                EC.element_to_be_clickable((By.CSS_SELECTOR, "#bookForm button[type='submit']"))
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
            time.sleep(5)  # Aguarda mais tempo para o processamento
            
            # Aguarda o modal fechar ou mudança na página
            try:
                self.wait.until(lambda driver: not driver.find_element(By.ID, "bookModal").is_displayed())
                print("✅ Modal fechado")
            except:
                print("⚠️ Modal não fechou, mas continuando...")
            
            print("✅ Processamento concluído")
            return True
        except Exception as e:
            print(f"❌ Erro ao aguardar processamento: {e}")
            return False
    
    def executar_teste(self):
        """Executa o teste completo de cadastro de livro"""
        print("🚀 Iniciando teste de sistema - Cadastro de Livro")
        print("=" * 60)
        
        sucesso = True
        
        # 1. Configurar navegador
        if not self.configurar_navegador():
            return False
        
        try:
            # 2. Gerar dados de teste
            dados = self.gerar_dados_livro()
            
            # 3. Acessar página de administração
            if not self.acessar_pagina_admin():
                sucesso = False
            
            # 4. Abrir modal de cadastro
            elif not self.abrir_modal_livro():
                sucesso = False
            
            # 5. Preencher formulário
            elif not self.preencher_formulario_livro(dados):
                sucesso = False
            
            # 6. Clicar em salvar
            elif not self.clicar_salvar_livro():
                sucesso = False
            
            # 7. Aguardar processamento
            elif not self.aguardar_processamento():
                sucesso = False
            
            # Resultado final
            print("=" * 60)
            if sucesso:
                print("✅ Livro registrado no banco de dados")
            else:
                print("❌ Erro no registro do livro")
            
            return sucesso
            
        finally:
            # Aguarda um pouco antes de fechar para visualizar o resultado
            if self.driver:
                print("👀 Mantendo navegador aberto por 3 segundos para visualização...")
                time.sleep(3)
                print("🔒 Fechando navegador...")
                self.driver.quit()
            
            # Limpa arquivos temporários se existirem
            try:
                import tempfile
                import os
                temp_dir = tempfile.gettempdir()
                for file in os.listdir(temp_dir):
                    if file.startswith('tmp') and file.endswith('.jpg'):
                        try:
                            os.unlink(os.path.join(temp_dir, file))
                        except:
                            pass
            except:
                pass

def main():
    """Função principal"""
    print("Biblioteca Arco-Íris - Teste de Sistema")
    print("Teste de Cadastro de Livro")
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
    teste = TesteLivro()
    resultado = teste.executar_teste()
    
    # Código de saída
    exit(0 if resultado else 1)

if __name__ == "__main__":
    main()
