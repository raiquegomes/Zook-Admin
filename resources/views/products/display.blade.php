<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Preços</title>
    <style>
        /* Fundo principal */
        body {
            margin: 0;
            height: 100vh;
            background: url('{{ asset("images/background.jpg") }}') no-repeat center center fixed;
            background-size: cover; /* Garante que o fundo cubra toda a tela */
            background-attachment: fixed; /* Faz o fundo ficar fixo enquanto rola a página */
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: 'Roboto', sans-serif;
        }

        /* Header */
        header {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .logo {
            max-height: 200px;
            max-width: 100%;
            object-fit: contain;
        }

        /* Contêiner principal */
        .product-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            width: 90%;
            margin-top: 20px;
            gap: 50px;
        }

        /* Estilo de seção por categoria */
        .category-section {
            width: 90%;
            margin: 20px 0;
        }

        .category-title {
            font-size: 60px;
            font-weight: bold;
            color: #FF4500;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            text-transform: uppercase;
            width: 30%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin-right: 50px;
        }

        /* Tabela de preços */
        table {
            width: 50%;
            border-collapse: collapse;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            border: 2px solid #FFD700;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        th {
            background-color: #FFD700;
            color: #000;
            padding: 12px;
            text-align: left;
            font-size: 18px;
            text-transform: uppercase;
            font-weight: bold;
            border-bottom: 2px solid #DAA520;
        }

        td {
            padding: 15px;
            font-size: 18px;
            text-align: center;
            border-bottom: 1px solid #DAA520;
        }

        tr:nth-child(odd) {
            background-color: rgba(255, 255, 255, 0.1);
        }

        tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.05);
        }

        tr:hover {
            background-color: rgba(255, 223, 0, 0.3);
            cursor: pointer;
        }

        /* Exibição do produto */
        .product-display {
            width: 48%;
            text-align: center;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .product-display h3 {
            font-size: 22px;
            color: #DAA520;
            margin-top: 10px;
        }

        .product-display img {
            max-width: 100%;
            max-height: 200PX;
            border-radius: 10px;
            object-fit: contain;
        }

        .product-display p {
            font-size: 48px;
            font-weight: bold;
            color: #FF4500;
            margin-top: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* Título da Oferta */
        .offer-title {
            background-color: #FF4500;
            color: white;
            font-size: 26px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        /* Destaca o produto que está sendo exibido no container "Oferta do Dia" */
        .active-product {
            font-weight: bold;
            color: #FF4500; /* Cor laranja */
            background-color: rgba(255, 223, 0, 0.3); /* Fundo levemente destacado */
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <img class="logo" src="{{ asset('images/oferta.png') }}" alt="Logo">
    </header>

    <!-- Título da Categoria -->
    <div class="category-title" id="category-title">Carregando...</div>

    <!-- Contêiner principal -->
    <div class="product-container">
        <!-- Tabela de Preços -->
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço (R$)</th>
                </tr>
            </thead>
            <tbody id="product-table"></tbody>
        </table>

        <!-- Exibição do Produto em Destaque -->
        <div class="product-display">
            <div class="offer-title">OFERTAS DO DIA</div>
            <h3 id="product-name">Nome do Produto</h3>
            <img id="product-image" src="" alt="Produto">
            <p id="product-price">R$ 0,00 /KG</p>
        </div>
    </div>

    <script>
        let categoriesWithProducts = [];
        let currentCategoryIndex = 0;
        let currentProductIndex = 0;

        // Função para formatar preços
        function formatPrice(price) {
            return price.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Função para carregar categorias e produtos
        function fetchCategoriesWithProducts() {
            fetch('/api/products-by-category')
                .then(response => response.json())
                .then(data => {
                    categoriesWithProducts = data;
                    currentCategoryIndex = 0; // Reinicia o índice ao carregar novos dados
                    currentProductIndex = 0;
                    updateCategoryDisplay();
                })
                .catch(error => console.error('Erro ao carregar os produtos:', error));
        }

        // Atualizar a exibição da tabela e dos produtos destacados
        function updateCategoryDisplay() {
            if (categoriesWithProducts.length === 0) return;

            const currentCategory = categoriesWithProducts[currentCategoryIndex];
            const categoryTitle = document.getElementById('category-title');
            const productTable = document.getElementById('product-table');
            const productName = document.getElementById('product-name');
            const productImage = document.getElementById('product-image');
            const productPrice = document.getElementById('product-price');

            // Atualiza o título da categoria
            categoryTitle.textContent = currentCategory.category;

            // Atualiza a tabela com os produtos
            productTable.innerHTML = '';
            currentCategory.products.forEach((product, index) => {
                const row = document.createElement('tr');
                const isActive = index === currentProductIndex ? 'class="active-product"' : ''; // Identifica o produto em destaque
                row.innerHTML = `
                    <td ${isActive}>${product.name}</td>
                    <td ${isActive}>R$ ${formatPrice(product.price)} /KG</td>
                `;
                productTable.appendChild(row);
            });

            // Atualiza o produto destacado (imagem e detalhes)
            if (currentCategory.products.length > 0) {
                const highlightedProduct = currentCategory.products[currentProductIndex];
                productName.textContent = highlightedProduct.name;
                productImage.src = highlightedProduct.image_url;
                productPrice.textContent = `R$ ${formatPrice(highlightedProduct.price)} /KG`;

                // Alterna para o próximo produto dentro da mesma categoria após 7 segundos
                currentProductIndex = (currentProductIndex + 1) % currentCategory.products.length;

                // Se todos os produtos da categoria foram exibidos, muda para a próxima categoria
                if (currentProductIndex === 0) {
                    currentCategoryIndex = (currentCategoryIndex + 1) % categoriesWithProducts.length;
                }
            }

            // Aguardar 7 segundos antes de atualizar o produto ou a categoria
            setTimeout(updateCategoryDisplay, 7000);
        }

        // Chamar a função de carregamento ao iniciar
        fetchCategoriesWithProducts();
    </script>

</body>
</html>
