const products = [
      { name: "Sepatu Olahraga", description: "Nyaman dan ringan", price: 350000, image: "sources/img/Sepatu olahraga.webp?text=Sepatu", category: "Olahraga" },
      { name: "Kaos Polos", description: "Cocok untuk sehari-hari", price: 75000, image: "sources/img/Kaos polos.webp?text=Kaos", category: "Pakaian" },
      { name: "Jam Tangan", description: "Elegan dan stylish", price: 550000, image: "sources/img/Jam tangan.webp?text=Jam", category: "Aksesoris" },
      { name: "Laptop Gaming", description: "Performa tinggi untuk game", price: 15000000, image: "sources/img/Laptop gaming.webp?text=Laptop", category: "Elektronik" },
      { name: "Kamera DSLR", description: "Hasil foto profesional", price: 8000000, image: "sources/img/Kamera DSLR.webp?text=Kamera", category: "Elektronik" },
      { name: "Celana Jeans", description: "Modis dan nyaman", price: 120000, image: "sources/img/Celana jeans.webp?text=Jeans", category: "Pakaian" },
      { name: "Tas Ransel", description: "Cocok untuk sekolah dan kerja", price: 180000, image: "sources/img/Tas ransel.webp?text=Tas", category: "Aksesoris" },
      { name: "Headphone", description: "Suara jernih dan bass mantap", price: 300000, image: "sources/img/Handphone.webp?text=Headphone", category: "Elektronik" },
      { name: "Bola Sepak", description: "Bahan kulit sintetis", price: 130000, image: "sources/img/Bola sepak.webp?text=Bola", category: "Olahraga" },
      { name: "Jaket Kulit", description: "Gaya dan tahan lama", price: 450000, image: "sources/img/Jaket kulit.webp?text=Jaket", category: "Pakaian" },
      { name: "Laptop Asus ROG", description: "Laptop gaming dengan RAM dan storage yang gede bet", price: 30000000, image: "sources/img/Laptop Asus ROG.webp?text=Jaket", category: "Elektronik" },
      { name: "VGA NVIDIA RTX 3060", description: "VGA gaming yang sangat ramah lingkungan", price: 9000000, image: "sources/img/VGA RTX 3060.webp?text=Jaket", category: "Elektronik" },
    ];

    const productContainer = document.getElementById("productContainer");
    const searchInput = document.getElementById("searchInput");
    const categoryFilter = document.getElementById("categoryFilter");

    function displayProducts(filteredProducts) {
      productContainer.innerHTML = "";
      if (filteredProducts.length === 0) {
        productContainer.innerHTML = '<p class="text-center">Produk tidak ditemukan.</p>';
        return;
      }

      filteredProducts.forEach(product => {
        const card = document.createElement("div");
        card.className = "col-md-4 mb-4";
        card.innerHTML = `
          <div class="card h-100">
            <img src="${product.image}" class="card-img-top" alt="${product.name}">
            <div class="card-body">
              <h5 class="card-title">${product.name}</h5>
              <p class="card-text">${product.description}</p>
              <p class="card-text text-primary">Rp ${product.price.toLocaleString()}</p>
              <span class="badge bg-secondary">${product.category}</span>
            </div>
          </div>
        `;
        productContainer.appendChild(card);
      });
    }

    function updateCategoryOptions() {
      const categories = [...new Set(products.map(p => p.category))];
      categories.forEach(cat => {
        const option = document.createElement("option");
        option.value = cat;
        option.textContent = cat;
        categoryFilter.appendChild(option);
      });
    }

    function filterProducts() {
      const search = searchInput.value.toLowerCase();
      const category = categoryFilter.value;
      const filtered = products.filter(p => {
        return (
          p.name.toLowerCase().includes(search) &&
          (category === "" || p.category === category)
        );
      });
      displayProducts(filtered);
    }

    searchInput.addEventListener("input", filterProducts);
    categoryFilter.addEventListener("change", filterProducts);

    // Inisialisasi
    updateCategoryOptions();
    displayProducts(products);