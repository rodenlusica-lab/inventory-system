from flask import Flask, request, jsonify
import pandas as pd

app = Flask(__name__)

# load dataset
df = pd.read_csv("Grocery_Inventory_and_Sales_Dataset.csv")


# OOP CLASS
class Sale:
    def __init__(self, price, qty):
        self.price = float(price)
        self.qty = int(qty)

    def total(self):
        return self.price * self.qty


@app.route("/process-sale", methods=["POST"])
def process_sale():
    data = request.json

    product_name = data["product"]
    qty = int(data["qty"])

    # pangita sa dataset
    product_row = df[df['Product'] == product_name]

    if product_row.empty:
        return jsonify({"error": "Product not found"})

    price = float(product_row.iloc[0]['Price'])

    sale = Sale(price, qty)

    return jsonify({
        "product": product_name,
        "price": price,
        "qty": qty,
        "total": sale.total()
    })


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=10000)