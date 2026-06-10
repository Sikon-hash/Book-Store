describe('E2E BookStore System Test', () => {
  beforeEach(() => {
    cy.task('resetData');
  });

  afterEach(() => {
    cy.task('resetData');
  });

  it('menampilkan halaman utama dengan katalog produk', () => {
    cy.visit('http://localhost:8000');

    cy.contains('Katalog Toko Buku').should('be.visible');
    cy.get('select[name="product_id"] option').should('have.length', 3);
  });

  it('form order memiliki semua field yang diperlukan', () => {
    cy.visit('http://localhost:8000');

    cy.get('input[name="quantity"]').should('be.visible');
    cy.get('textarea[name="address"]').should('be.visible');
    cy.get('#submitBtn').contains('Beli Sekarang').should('be.visible');
  });

  it('submit form menampilkan ringkasan pesanan', () => {
    cy.visit('http://localhost:8000');

    cy.get('input[name="quantity"]').type('2');
    cy.get('textarea[name="address"]').type('Jl. Merdeka No. 1');
    cy.get('#submitBtn').click();

    cy.contains('Pesanan Berhasil Diproses').should('be.visible');
  });
});
