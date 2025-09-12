*
 * MASCARAMENTO BRASILEIRO - Biblioteca Arco-Íris
 * Funções para aplicar máscaras em campos de formulário
 */

// Máscara para CPF (000.000.000-00)
function mascaraCPF(input) {
    let value = input.value.replace(/\D/g, '');
    
    if (value.length <= 11) {
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }
    
    input.value = value;
}

// Máscara para CNPJ (00.000.000/0000-00)
function mascaraCNPJ(input) {
    let value = input.value.replace(/\D/g, '');
    
    if (value.length <= 14) {
        value = value.replace(/(\d{2})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1/$2');
        value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    }
    
    input.value = value;
}

// Máscara para CPF/CNPJ (detecta automaticamente)
function mascaraCPFCNPJ(input) {
    let value = input.value.replace(/\D/g, '');
    
    if (value.length <= 11) {
        // CPF
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    } else {
        // CNPJ
        value = value.replace(/(\d{2})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1/$2');
        value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    }
    
    input.value = value;
}

// Máscara para telefone ((00) 00000-0000 ou (00) 0000-0000)
function mascaraTelefone(input) {
    let value = input.value.replace(/\D/g, '');
    
    if (value.length <= 11) {
        if (value.length <= 10) {
            // Telefone fixo (00) 0000-0000
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{4})(\d{1,4})$/, '$1-$2');
        } else {
            // Celular (00) 00000-0000
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
        }
    }
    
    input.value = value;
}

// Máscara para CEP (00000-000)
function mascaraCEP(input) {
    let value = input.value.replace(/\D/g, '');
    
    if (value.length <= 8) {
        value = value.replace(/(\d{5})(\d{1,3})$/, '$1-$2');
    }
    
    input.value = value;
}

// Máscara para RG (00.000.000-0)
function mascaraRG(input) {
    let value = input.value.replace(/\D/g, '');
    
    if (value.length <= 9) {
        value = value.replace(/(\d{2})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,1})$/, '$1-$2');
    }
    
    input.value = value;
}

// Máscara para número de casa (apenas números)
function mascaraNumero(input) {
    input.value = input.value.replace(/\D/g, '');
}

// Máscara para nome (apenas letras e espaços)
function mascaraNome(input) {
    input.value = input.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '');
}

// Máscara para complemento (letras, números e espaços)
function mascaraComplemento(input) {
    input.value = input.value.replace(/[^A-Za-zÀ-ÿ0-9\s]/g, '');
}

// Função para aplicar máscara baseada no tipo do campo
function aplicarMascara(input) {
    const tipo = input.getAttribute('data-mascara') || input.name;
    
    switch(tipo) {
        case 'cpf':
            mascaraCPF(input);
            break;
        case 'cnpj':
            mascaraCNPJ(input);
            break;
        case 'cpfCnpj':
        case 'cpf_cnpj':
            mascaraCPFCNPJ(input);
            break;
        case 'telefone':
        case 'phone':
            mascaraTelefone(input);
            break;
        case 'cep':
            mascaraCEP(input);
            break;
        case 'rg':
            mascaraRG(input);
            break;
        case 'numero':
        case 'numCasa':
            mascaraNumero(input);
            break;
        case 'nome':
            mascaraNome(input);
            break;
        case 'complemento':
            mascaraComplemento(input);
            break;
    }
}

// Função para inicializar máscaras em todos os campos
function inicializarMascaras() {
    // Campos com data-mascara
    document.querySelectorAll('[data-mascara]').forEach(input => {
        input.addEventListener('input', function() {
            aplicarMascara(this);
        });
    });
    
    // Campos por nome
    document.querySelectorAll('input[name="cpf"]').forEach(input => {
        input.addEventListener('input', function() {
            mascaraCPF(this);
        });
    });
    
    document.querySelectorAll('input[name="telefone"]').forEach(input => {
        input.addEventListener('input', function() {
            mascaraTelefone(this);
        });
    });
    
    document.querySelectorAll('input[name="cep"]').forEach(input => {
        input.addEventListener('input', function() {
            mascaraCEP(this);
        });
    });
    
    document.querySelectorAll('input[name="cpfCnpj"], input[name="cpf_cnpj"]').forEach(input => {
        input.addEventListener('input', function() {
            mascaraCPFCNPJ(this);
        });
    });
    
    document.querySelectorAll('input[name="nome"]').forEach(input => {
        input.addEventListener('input', function() {
            mascaraNome(this);
        });
    });
    
    document.querySelectorAll('input[name="numCasa"], input[name="numero"]').forEach(input => {
        input.addEventListener('input', function() {
            mascaraNumero(this);
        });
    });
    
    document.querySelectorAll('input[name="complemento"]').forEach(input => {
        input.addEventListener('input', function() {
            mascaraComplemento(this);
        });
    });
}

// Função para remover máscara (retorna apenas números)
function removerMascara(valor) {
    return valor.replace(/\D/g, '');
}

// Função para validar CPF
function validarCPF(cpf) {
    cpf = removerMascara(cpf);
    
    if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
        return false;
    }
    
    let soma = 0;
    for (let i = 0; i < 9; i++) {
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    let resto = 11 - (soma % 11);
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(9))) return false;
    
    soma = 0;
    for (let i = 0; i < 10; i++) {
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    resto = 11 - (soma % 11);
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(10))) return false;
    
    return true;
}

// Função para validar CNPJ
function validarCNPJ(cnpj) {
    cnpj = removerMascara(cnpj);
    
    if (cnpj.length !== 14 || /^(\d)\1{13}$/.test(cnpj)) {
        return false;
    }
    
    let tamanho = cnpj.length - 2;
    let numeros = cnpj.substring(0, tamanho);
    let digitos = cnpj.substring(tamanho);
    let soma = 0;
    let pos = tamanho - 7;
    
    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }
    
    let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0)) return false;
    
    tamanho = tamanho + 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;
    
    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }
    
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1)) return false;
    
    return true;
}

// Inicializar máscaras quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    inicializarMascaras();
});
