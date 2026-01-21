-- =====================================================
-- Livraria Online - Script de Criação da Base de Dados
-- =====================================================

-- Criar base de dados
CREATE DATABASE IF NOT EXISTS livraria_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE livraria_db;

-- =====================================================
-- Tabela: users (Utilizadores)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- Tabela: categories (Categorias)
-- =====================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- Tabela: subcategories (Subcategorias)
-- =====================================================
CREATE TABLE IF NOT EXISTS subcategories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- Tabela: books (Livros)
-- =====================================================
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    category_id INT,
    subcategory_id INT,
    stock INT DEFAULT 0,
    featured BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (subcategory_id) REFERENCES subcategories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================
-- Tabela: cart (Carrinho de Compras)
-- =====================================================
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- Tabela: orders (Encomendas)
-- =====================================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT 'Pagamento na Entrega',
    shipping_address TEXT NOT NULL,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- Tabela: order_items (Itens das Encomendas)
-- =====================================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- Tabela: contacts (Mensagens de Contacto)
-- =====================================================
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- Inserir Utilizador Admin Padrão
-- Password: admin123 (hash bcrypt)
-- =====================================================
INSERT INTO users (name, email, password, role) VALUES 
('Administrador', 'admin@livraria.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- =====================================================
-- Inserir Categorias de Exemplo
-- =====================================================
INSERT INTO categories (name, description) VALUES 
('Ficção', 'Livros de ficção, romances e novelas'),
('Não-Ficção', 'Livros de não-ficção, biografias e ensaios'),
('Infantil', 'Livros para crianças e jovens'),
('Técnico', 'Livros técnicos e científicos'),
('Arte e Fotografia', 'Livros de arte, design e fotografia');

-- =====================================================
-- Inserir Subcategorias de Exemplo
-- =====================================================
INSERT INTO subcategories (category_id, name, description) VALUES 
(1, 'Romance', 'Romances literários'),
(1, 'Ficção Científica', 'Ficção científica e fantasia'),
(1, 'Mistério', 'Livros de mistério e thriller'),
(2, 'Biografia', 'Biografias e autobiografias'),
(2, 'História', 'Livros de história'),
(2, 'Autoajuda', 'Livros de desenvolvimento pessoal'),
(3, 'Contos Infantis', 'Histórias para crianças'),
(3, 'Jovem Adulto', 'Livros para adolescentes'),
(4, 'Programação', 'Livros de programação e tecnologia'),
(4, 'Ciências', 'Livros científicos'),
(5, 'Design', 'Livros de design gráfico'),
(5, 'Fotografia', 'Livros de fotografia');

-- =====================================================
-- Inserir Livros de Exemplo
-- =====================================================
INSERT INTO books (title, author, description, price, image, category_id, subcategory_id, stock, featured) VALUES 
('O Senhor dos Anéis', 'J.R.R. Tolkien', 'Uma épica aventura na Terra Média onde Frodo deve destruir o Um Anel.', 29.99, 'senhor-aneis.jpg', 1, 2, 50, TRUE),
('1984', 'George Orwell', 'Uma distopia que retrata um futuro totalitário assustadoramente atual.', 15.99, '1984.jpg', 1, 2, 30, TRUE),
('O Código Da Vinci', 'Dan Brown', 'Um thriller que mistura arte, religião e conspirações.', 19.99, 'codigo-davinci.jpg', 1, 3, 25, FALSE),
('Steve Jobs', 'Walter Isaacson', 'A biografia autorizada do visionário fundador da Apple.', 24.99, 'steve-jobs.jpg', 2, 4, 20, TRUE),
('Sapiens', 'Yuval Noah Harari', 'Uma breve história da humanidade.', 22.99, 'sapiens.jpg', 2, 5, 40, TRUE),
('O Pequeno Príncipe', 'Antoine de Saint-Exupéry', 'Um clássico atemporal sobre amizade e amor.', 12.99, 'pequeno-principe.jpg', 3, 7, 100, TRUE),
('Harry Potter e a Pedra Filosofal', 'J.K. Rowling', 'O início da saga do jovem bruxo Harry Potter.', 18.99, 'harry-potter.jpg', 3, 8, 75, TRUE),
('Clean Code', 'Robert C. Martin', 'Guia essencial para escrever código limpo e manutenível.', 39.99, 'clean-code.jpg', 4, 9, 15, FALSE),
('Uma Breve História do Tempo', 'Stephen Hawking', 'Explorando os mistérios do universo.', 21.99, 'breve-historia.jpg', 4, 10, 35, FALSE),
('O Poder do Hábito', 'Charles Duhigg', 'Por que fazemos o que fazemos na vida e nos negócios.', 17.99, 'habito.jpg', 2, 6, 45, TRUE);
