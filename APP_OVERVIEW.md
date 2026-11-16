# Precious Metal App Overview

## Purpose

The application allows customers to own, buy, sell, transfer, and withdraw precious-metal assets. Gold is stored as weight, while its monetary value is calculated from the current configured price.

## Core concepts

- **Customer:** Authenticated user who owns an asset wallet.
- **Asset wallet:** Stores gold quantity, including available and reserved weight.
- **Rate:** Current buying or selling price per gram in GHS.
- **Quote:** A short-lived price snapshot for a buy, sell, or transfer operation.
- **Transaction:** Permanent record of gold movement.
- **Withdrawal:** A request to convert gold to cash and pay the customer.

The customer’s primary balance is gold weight. Its estimated value is calculated as:

    gold weight × current price per gram

There is currently no separate internal cash-wallet balance.

## Customer flow

### 1. Authentication

Customers register or log in through the customer authentication endpoints. Authenticated asset routes use Sanctum.

### 2. View wallets and balance

    GET /v0/customers/assets/wallets
    GET /v0/customers/assets/balance
    GET /v0/customers/assets/rate

The wallet response includes the numeric database ID and the public wallet_reference UUID. Customer withdrawal requests use wallet_reference.

### 3. Buy gold

    POST /v0/customers/assets/purchases/quote
    POST /v0/customers/assets/purchases

The quote calculates the gold weight from the requested cash amount and the current buying price. Confirming the purchase credits gold to the customer’s asset wallet.

The intended production flow is:

    Create quote → receive and verify external payment → credit gold → record completed transaction

Payment verification is not fully implemented yet; the current purchase operation credits gold after quote confirmation.

### 4. Sell gold

    POST /v0/customers/assets/sales/quote
    POST /v0/customers/assets/sales

The quote calculates the gross value, fee, and net cash proceeds using the selling price. Confirming the sale deducts gold and records the calculated proceeds.

An external payout or internal cash credit is not currently implemented.

### 5. Transfer gold

    POST /v0/customers/assets/transfers/recipient-enquiry
    POST /v0/customers/assets/transfers/quote
    POST /v0/customers/assets/transfers

The sender’s wallet is debited and the recipient’s wallet is credited inside a database transaction. Transfer fees are represented as gold weight.

### 6. Withdraw gold value

    POST /v0/customers/assets/withdrawals

The customer supplies the wallet’s wallet_reference UUID, the gold amount, and the conversion type. The app:

1. Verifies the wallet belongs to the authenticated customer.
2. Verifies that the wallet is active.
3. Deducts the amount from available gold.
4. Reserves the amount.
5. Calculates the cash value.
6. Creates a pending withdrawal.

Platform staff can review and approve withdrawals:

    GET /v0/assets/withdrawals
    POST /v0/assets/withdrawals/{id}/approve

The current approval flow releases the reserved gold, but does not yet initiate an external payout.

## Platform responsibilities

Platform managers can manage:

- Customers and platform users.
- Gold rates.
- Fees.
- Asset wallets.
- Quotes and transactions.
- Withdrawal approvals.

## Current limitations

The following settlement features still need implementation before production use:

- External payment confirmation before crediting purchased gold.
- Bank or mobile-money payout processing for sales and withdrawals.
- Payment and payout statuses, callbacks, and reconciliation.
- Quote ownership, expiry, single-use, and replay protection.
- A cash wallet and cash ledger if sale proceeds should remain inside the app.

If all purchases and sales settle directly through external payment providers, an internal cash wallet is optional. A verified payment/payout ledger is still required.
