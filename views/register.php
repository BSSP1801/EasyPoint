<div class="register-container">
    <h2>Create your Account</h2>
    <form action="index.php?action=register" method="POST">
        <label for="role">I want to register as:</label>
        <select name="role" id="role-selector" onchange="toggleStoreFields()" required>
            <option value="user">Customer (Client)</option>
            <option value="store">Business (Store)</option>
        </select>

        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <div id="store-fields" style="display: none; border-top: 1px solid #ccc; margin-top: 15px; padding-top: 10px;">
            <h3>Business Information</h3>
            <input type="text" name="business_name" placeholder="Business Name">
            <input type="text" name="address" placeholder="Address">
            <input type="text" name="postal_code" placeholder="Postal Code">
        </div>

        <button type="submit">Register</button>
    </form>
</div>

<script>
function toggleStoreFields() {
    const selector = document.getElementById('role-selector');
    const storeFields = document.getElementById('store-fields');
    storeFields.style.display = (selector.value === 'store') ? 'block' : 'none';
}
</script>