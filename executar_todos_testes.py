#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Executador de Todos os Testes - Biblioteca Arco-Íris

Este script executa todos os testes de sistema disponíveis:
1. Teste de Registro de Usuário
2. Teste de Cadastro de Fornecedor  
3. Teste de Cadastro de Livro
"""

import sys
import time
from registro import TesteRegistro
from teste_fornecedor import TesteFornecedor
from teste_livro import TesteLivro

def executar_todos_testes():
    """Executa todos os testes de sistema"""
    print("🚀 BIBLIOTECA ARCO-ÍRIS - SUITE DE TESTES COMPLETA")
    print("=" * 70)
    print()
    
    resultados = {}
    
    # 1. Teste de Registro de Usuário
    print("📋 TESTE 1/3: REGISTRO DE USUÁRIO")
    print("-" * 50)
    try:
        teste_registro = TesteRegistro()
        resultados['registro'] = teste_registro.executar_teste()
    except Exception as e:
        print(f"❌ Erro no teste de registro: {e}")
        resultados['registro'] = False
    
    print("\n" + "=" * 70 + "\n")
    
    # 2. Teste de Cadastro de Fornecedor
    print("📋 TESTE 2/3: CADASTRO DE FORNECEDOR")
    print("-" * 50)
    
    try:
        teste_fornecedor = TesteFornecedor()
        resultados['fornecedor'] = teste_fornecedor.executar_teste()
    except Exception as e:
        print(f"❌ Erro no teste de fornecedor: {e}")
        resultados['fornecedor'] = False
    
    print("\n" + "=" * 70 + "\n")
    
    # 3. Teste de Cadastro de Livro
    print("📋 TESTE 3/3: CADASTRO DE LIVRO")
    print("-" * 50)
    
    try:
        teste_livro = TesteLivro()
        resultados['livro'] = teste_livro.executar_teste()
    except Exception as e:
        print(f"❌ Erro no teste de livro: {e}")
        resultados['livro'] = False
    
    # Relatório final
    print("\n" + "=" * 70)
    print("📊 RELATÓRIO FINAL DOS TESTES")
    print("=" * 70)
    
    total_testes = len(resultados)
    testes_sucesso = sum(1 for resultado in resultados.values() if resultado)
    
    print(f"Total de testes executados: {total_testes}")
    print(f"Testes bem-sucedidos: {testes_sucesso}")
    print(f"Testes falharam: {total_testes - testes_sucesso}")
    print()
    
    # Detalhes por teste
    print("Detalhes por teste:")
    print("-" * 30)
    
    status_registro = "✅ PASSOU" if resultados.get('registro', False) else "❌ FALHOU"
    print(f"1. Registro de Usuário: {status_registro}")
    
    status_fornecedor = "✅ PASSOU" if resultados.get('fornecedor', False) else "❌ FALHOU"
    print(f"2. Cadastro de Fornecedor: {status_fornecedor}")
    
    status_livro = "✅ PASSOU" if resultados.get('livro', False) else "❌ FALHOU"
    print(f"3. Cadastro de Livro: {status_livro}")
    
    print()
    
    # Resultado geral
    if testes_sucesso == total_testes:
        print("🎉 TODOS OS TESTES PASSARAM!")
        print("✅ O sistema está funcionando corretamente")
        return True
    else:
        print("⚠️ ALGUNS TESTES FALHARAM!")
        print("❌ Verifique os logs acima para identificar problemas")
        return False

def main():
    """Função principal"""
    print("Biblioteca Arco-Íris - Suite de Testes Completa")
    print("Este script executará todos os testes de sistema disponíveis.")
    print()
    
    # Verifica se o Selenium está instalado
    try:
        import selenium
        print(f"✅ Selenium versão {selenium.__version__} detectado")
    except ImportError:
        print("❌ Selenium não está instalado!")
        print("Execute: pip install selenium")
        return
    
    print()
    print("⚠️ IMPORTANTE:")
    print("- Certifique-se de que o XAMPP está rodando")
    print("- Todos os testes usam versões públicas (não requerem login)")
    print("- Os testes podem demorar alguns minutos para completar")
    print()
    
    resposta = input("Deseja continuar? (S/N): ").strip().upper()
    if resposta != 'S':
        print("Testes cancelados pelo usuário.")
        return
    
    # Executa todos os testes
    sucesso_geral = executar_todos_testes()
    
    # Código de saída
    exit(0 if sucesso_geral else 1)

if __name__ == "__main__":
    main()
