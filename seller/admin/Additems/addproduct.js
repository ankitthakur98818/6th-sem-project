
document.addEventListener("DOMContentLoaded", loadProducts);

function loadProducts() {
  fetch("get_products.php")
    .then(res => res.json())
    .then(data => {
      const list = document.getElementById("productsList");
      list.innerHTML = "";

      document.getElementById("totalProducts").textContent = data.length;
      document.getElementById("saleProducts").textContent =
        data.filter(p => p.on_sale === 't').length;

      data.forEach(p => {
        list.innerHTML += `
          <div class="product-item">
            <img src="${p.image_path}">
            <div class="product-info">
              <h4>${p.name}
                ${p.on_sale === 't' ? '<span class="badge badge-sale">SALE</span>' : ''}
              </h4>
              <p>Rs ${p.price} / kg</p>
              <span class="badge badge-category">${p.category}</span>
            </div>
          </div>
        `;
      });
    });
}

