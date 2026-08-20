let cart = document.querySelector('.cart');
let body = document.querySelector('body');
let listProductsHTML = document.querySelector('.listProduct');
let listCartHTML = document.querySelector('.listCart');
let cartSpan = document.querySelector('.cart span');

let listProducts = [];
let carts = [];

cart.addEventListener('click', () => {
    body.classList.toggle('showCart')
})

const addDataToHTML = () => {
    listProductsHTML.innerHTML = '';
    if(listProducts.length > 0){
        listProducts.forEach(product => {
            let newProduct = document.createElement('div');
            newProduct.classList.add('item');
            newProduct.dataset.id = product.id;
            newProduct.innerHTML = `
                <img src="${product.image}" alt="">
                <p>${product.category}</p>
                <h2>${product.name}</h2>
                <h3>${product.brand}</h3>
                <div class="price">₱${product.price}</div>
                <button class="addcart">Add to Cart</button>
            ` ;
            listProductsHTML.appendChild(newProduct);
        })
    }
}
listProductsHTML.addEventListener('click', (event) => {
    let positionClick = event.target;
    if(positionClick.classList.contains('addCart')){
        let product_id = positionClick.parentElement.dataset.id;
        addToCart(product_id);
    }
})

const addToCart = (product_id) => {
    let positionThisProductInCart = carts.findIndex((value) => value.product_id == product_id);
    if(carts.length <= 0){
        carts = [{
            product_id: product_id,
            quantity: 1
        }]
    }
    else if(positionThisProductInCart < 0){
        carts.push({
            product_id: product_id,
            quantity: 1
        });
    }
    else{
        carts[positionThisProductInCart].quantity = carts[positionThisProductInCart].quantity + 1;
    }
    addCartToHTML();
}
const addCartToHTML = () => {
    listCartHTML.innerHTML = '';
    if(carts.length > 0){
        carts.forEach(cart => {
            let newCart = document.createElement('div');
            newCart.classList.add('item');
            let positionProduct = listProducts.findIndex((value) => value.id == cart.product_id);
            let info = listProducts[positionProduct];
            newCart.innerHTML = `
                <div class="image">
                    <img src="${info.image}" alt="">
                </div>
                <div class="name">
                    ${info.name}
                </div>
                <div class="brand">
                    ${info.brand}
                </div>
                <div class="category">
                    ${info.category}
                </div>
                <div class="totalPrice">
                    ₱${info.price * cart.quantity}
                </div>
                <div class="quantity">
                    <span class="minus"><</span>
                    <span>${cart.quantity}</span>
                    <span class="plus">></span>
                </div>
            `;
        listCartHTML.appendChild(newCart);
        })
    }
}
const initApp = () => {
    fetch('product.json')
    .then(response => response.json())
    .then(data => {
        listProducts = data;
        addDataToHTML();
    })
}
initApp();
