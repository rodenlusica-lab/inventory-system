from flask import Flask, request, jsonify
import mysql.connector

app = Flask(__name__)

def get_db():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="cloud_system"  # ✅ FIXED
    )

@app.route("/process-sale", methods=["POST"])
def process_sale():
    try:
        data = request.json

        product_id = data.get("product_id")
        qty = int(data.get("qty", 0))

        if not product_id or qty <= 0:
            return jsonify({"error": "Invalid input"})

        db = get_db()
        cursor = db.cursor(dictionary=True)

        print("👉 RECEIVED:", product_id, qty)

        # GET PRODUCT
        cursor.execute("SELECT * FROM products WHERE Product_ID=%s", (product_id,))
        product = cursor.fetchone()

        if not product:
            return jsonify({"error": "Product not found"})

        current_stock = int(product["Stock_Quantity"])
        price = float(product["Unit_Price"])
        name = product["Product_Name"]

        print("👉 PRODUCT:", name, current_stock)

        if current_stock < qty:
            return jsonify({"error": "Not ENOUGH stock"})

        new_stock = current_stock - qty
        total = price * qty

        # UPDATE STOCK
        cursor.execute(
            "UPDATE products SET Stock_Quantity=%s WHERE Product_ID=%s",
            (new_stock, product_id)
        )

        # SAVE SALE
        cursor.execute(
            "INSERT INTO sales_records (product_name, quantity, total_price, sale_date) VALUES (%s,%s,%s,NOW())",
            (name, qty, total)
        )

        # LOG
        cursor.execute(
            "INSERT INTO activity_logs (action, product_name, details) VALUES (%s,%s,%s)",
            ("SALE", name, f"Sold {qty} pcs (₱{total})")
        )

        db.commit()

        print("✅ SALE SAVED")

        cursor.close()
        db.close()

        return jsonify({
            "product": name,
            "price": price,
            "qty": qty,
            "total": total,
            "remaining_stock": new_stock
        })

    except Exception as e:
        print("❌ ERROR:", str(e))
        return jsonify({"error": str(e)})
    
if __name__ == "__main__":
    app.run(host="0.0.0.0", port=10000)
