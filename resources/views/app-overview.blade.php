<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Precious Metal App Overview</title>
    <style>
        :root { color-scheme: dark; --bg: #0d1117; --panel: #161b22; --text: #e6edf3; --muted: #9da7b3; --gold: #e3b341; --line: #30363d; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--text); font: 16px/1.65 Inter, ui-sans-serif, system-ui, sans-serif; }
        main { max-width: 960px; margin: 0 auto; padding: 56px 24px 80px; }
        header { border-bottom: 1px solid var(--line); margin-bottom: 36px; padding-bottom: 28px; }
        h1 { color: var(--gold); font-size: clamp(2rem, 5vw, 3.4rem); line-height: 1.1; margin: 0 0 12px; }
        h2 { margin-top: 42px; color: var(--gold); }
        h3 { margin-top: 28px; }
        p, li { color: var(--muted); }
        strong { color: var(--text); }
        code { background: #21262d; border-radius: 5px; color: #f0d597; padding: 2px 6px; }
        pre { background: var(--panel); border: 1px solid var(--line); border-radius: 8px; color: #f0d597; overflow-x: auto; padding: 16px; }
        .intro { font-size: 1.15rem; max-width: 760px; }
        .card { background: var(--panel); border: 1px solid var(--line); border-radius: 10px; margin: 18px 0; padding: 20px 24px; }
        .endpoint { color: #8bd5ca; font-family: ui-monospace, SFMono-Regular, monospace; }
        .warning { border-left: 4px solid var(--gold); padding-left: 18px; }
        a { color: #79c0ff; }
    </style>
</head>
<body>
<main>
    <header>
        <h1>Precious Metal App</h1>
        <p class="intro">A customer platform for owning, buying, selling, transferring, and withdrawing precious-metal assets.</p>
    </header>

    <section>
        <h2>How it works</h2>
        <p>Gold is stored as weight. Its estimated monetary value is calculated from the current configured price:</p>
        <pre>gold weight × current price per gram</pre>
        <p>The customer’s primary balance is gold. The application currently has no separate internal cash-wallet balance.</p>
    </section>

    <section>
        <h2>Core concepts</h2>
        <div class="card">
            <ul>
                <li><strong>Asset wallet:</strong> Stores available and reserved gold weight.</li>
                <li><strong>Rate:</strong> Current buying or selling price per gram in GHS.</li>
                <li><strong>Quote:</strong> A short-lived price snapshot for a transaction.</li>
                <li><strong>Transaction:</strong> A permanent record of gold movement.</li>
                <li><strong>Withdrawal:</strong> A request to convert gold value into cash and pay the customer.</li>
            </ul>
        </div>
    </section>

    <section>
        <h2>Customer flow</h2>
        <h3>View wallet and balance</h3>
        <p class="endpoint">GET /v0/customers/assets/wallets</p>
        <p class="endpoint">GET /v0/customers/assets/balance</p>
        <p class="endpoint">GET /v0/customers/assets/rate</p>
        <p>Wallet responses include a public <code>wallet_reference</code> UUID. Withdrawal requests use that identifier.</p>

        <h3>Buy gold</h3>
        <p class="endpoint">POST /v0/customers/assets/purchases/quote</p>
        <p class="endpoint">POST /v0/customers/assets/purchases</p>
        <p>The quote calculates gold weight from the requested cash amount and buying price. The intended production flow is:</p>
        <pre>Create quote → verify external payment → credit gold → record transaction</pre>
        <p class="warning">Payment verification is not fully implemented yet. The current purchase operation credits gold after quote confirmation.</p>

        <h3>Sell gold</h3>
        <p class="endpoint">POST /v0/customers/assets/sales/quote</p>
        <p class="endpoint">POST /v0/customers/assets/sales</p>
        <p>The quote calculates gross value, fees, and net proceeds. Confirming the sale deducts gold and records the calculated cash amount. External payout is not currently implemented.</p>

        <h3>Transfer gold</h3>
        <p class="endpoint">POST /v0/customers/assets/transfers/recipient-enquiry</p>
        <p class="endpoint">POST /v0/customers/assets/transfers/quote</p>
        <p class="endpoint">POST /v0/customers/assets/transfers</p>
        <p>The sender is debited and the recipient is credited inside a database transaction.</p>

        <h3>Withdraw gold value</h3>
        <p class="endpoint">POST /v0/customers/assets/withdrawals</p>
        <p>The app verifies ownership and active status, deducts available gold, reserves it, calculates the cash value, and creates a pending withdrawal.</p>
        <p>Platform staff review withdrawals through:</p>
        <p class="endpoint">GET /v0/assets/withdrawals</p>
        <p class="endpoint">POST /v0/assets/withdrawals/{id}/approve</p>
        <p class="warning">Approval currently releases reserved gold but does not initiate an external payout.</p>
    </section>

    <section>
        <h2>What remains to complete</h2>
        <ul>
            <li>External payment confirmation before purchased gold is credited.</li>
            <li>Bank or mobile-money payout processing.</li>
            <li>Payment and payout statuses, callbacks, and reconciliation.</li>
            <li>Quote ownership, expiry, single-use, and replay protection.</li>
            <li>An internal cash wallet and ledger, if sale proceeds should remain in the app.</li>
        </ul>
    </section>
</main>
</body>
</html>
