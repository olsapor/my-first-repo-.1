function addToCart(productId) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('ថែមចូលកន្ត្រកជោគជ័យ!');
            updateCartCount(); // Update លេខក្នុងកន្ត្រកភ្លាមៗ
        } else {
            // បង្ហាញសារ Error ពិតប្រាកដដែលមកពី PHP (ឧទាហរណ៍៖ "សូមចូលប្រើប្រាស់ជាមុនសិន")
            alert(data.message); 
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('មានបញ្ហាបច្ចេកទេស៖ ' + error);
    });
}