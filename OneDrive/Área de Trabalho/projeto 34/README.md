# Projeto de Testes Cypress - EBAC Agenda de Contatos

## Descrição
Este projeto contém testes automatizados utilizando **Cypress** para testar a aplicação [EBAC Agenda de Contatos](https://ebac-agenda-contatos-tan.vercel.app/).

## Funcionalidades Testadas

- ✅ **Inclusão** de contato
- ✅ **Alteração** de contato  
- ✅ **Remoção** de contato

## Instalação

```bash
npm install
```

## Executar Testes

### Modo Headless (Terminal)
```bash
npm test
```

### Modo Interativo (Cypress GUI)
```bash
npm run cypress:open
```

## Estrutura do Projeto

```
├── cypress/
│   └── e2e/
│       └── agenda.cy.js    # Testes da agenda de contatos
├── cypress.config.js       # Configuração do Cypress
├── package.json            # Dependências do projeto
└── README.md               # Este arquivo
```

## Tecnologias

- **Cypress v15.8.2**
- **Node.js**

## Autor
Exercício EBAC - Módulo Cypress
