/// <reference types="cypress" />

describe('Agenda de Contatos', () => {
    beforeEach(() => {
        cy.visit('/');
    });

    it('Deve adicionar um contato', () => {
        cy.get('input[type="text"]').type('Teste Cypress');
        cy.get('input[type="email"]').type('teste@cypress.com');
        cy.get('input[type="tel"]').type('11999999999');

        cy.contains('Adicionar').click();

        cy.contains('Teste Cypress').should('be.visible');
        cy.contains('teste@cypress.com').should('be.visible');
        cy.contains('11999999999').should('be.visible');
    });

    it('Deve editar um contato', () => {
        // Create contact to ensure existence
        cy.get('input[type="text"]').type('Contato Editável');
        cy.get('input[type="email"]').type('editar@teste.com');
        cy.get('input[type="tel"]').type('11888888888');
        cy.contains('Adicionar').click();

        // Click Edit (assuming .edit class)
        // If the buttons are strictly icon-based without text, we rely on classes. 
        // Based on typical EBAC exercises, they use .edit and .delete classes.
        cy.contains('Contato Editável').parents('.contato').find('.edit').click();

        // Edit form usually replaces the inputs or opens a modal. 
        // Assuming it prefills the main inputs:
        cy.get('input[type="text"]').clear().type('Contato Editado');
        cy.contains('Salvar').click(); // Expecting 'Adicionar' to change to 'Salvar'

        cy.contains('Contato Editado').should('be.visible');
        cy.contains('Contato Editável').should('not.exist');
    });

    it('Deve remover um contato', () => {
        cy.get('input[type="text"]').type('Contato Deletável');
        cy.get('input[type="email"]').type('deletar@teste.com');
        cy.get('input[type="tel"]').type('11777777777');
        cy.contains('Adicionar').click();

        cy.contains('Contato Deletável').parents('.contato').find('.delete').click();

        cy.contains('Contato Deletável').should('not.exist');
    });
});
