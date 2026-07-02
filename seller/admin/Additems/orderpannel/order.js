async function loadOrders() {
    const res = await fetch("backend/get_orders.php");
    const orders = await res.json();

    const table = document.getElementById("ordersTable");
    table.innerHTML = '';

    orders.forEach(order => {
        const row = document.createElement("tr");

        row.innerHTML = `
            <td>${order.id}</td>
            <td>${order.first_name} ${order.last_name}</td>
            <td>${order.city}</td>
            <td>Rs. ${order.total}</td>
            <td>${order.status}</td>
            <td>
                <select onchange="updateStatus(${order.id}, this.value)">
                    <option ${order.status==='Pending'?'selected':''}>Pending</option>
                    <option ${order.status==='Processing'?'selected':''}>Processing</option>
                    <option ${order.status==='Shipped'?'selected':''}>Shipped</option>
                    <option ${order.status==='Delivered'?'selected':''}>Delivered</option>
                </select>
            </td>
        `;

        table.appendChild(row);
    });
}

async function updateStatus(id, status) {
    await fetch("backend/update_status.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({id, status})
    });

    loadOrders();
}

loadOrders();