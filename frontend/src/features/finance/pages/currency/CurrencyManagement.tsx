import React, { useState } from 'react';
import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
} from 'recharts';

interface Currency {
  id: string;
  code: string;
  name: string;
  symbol: string;
  country: string;
  isBase: boolean;
  status: string;
}

interface ExchangeRate {
  id: string;
  baseCurrency: string;
  targetCurrency: string;
  rate: number;
  inverseRate: number;
  effectiveDate: string;
  source: string;
}

const currencies: Currency[] = [
  { id: '1', code: 'BDT', name: 'Bangladeshi Taka', symbol: '৳', country: 'Bangladesh', isBase: true, status: 'active' },
  { id: '2', code: 'USD', name: 'US Dollar', symbol: '$', country: 'United States', isBase: false, status: 'active' },
  { id: '3', code: 'EUR', name: 'Euro', symbol: '€', country: 'European Union', isBase: false, status: 'active' },
  { id: '4', code: 'GBP', name: 'British Pound', symbol: '£', country: 'United Kingdom', isBase: false, status: 'active' },
  { id: '5', code: 'INR', name: 'Indian Rupee', symbol: '₹', country: 'India', isBase: false, status: 'active' },
  { id: '6', code: 'SAR', name: 'Saudi Riyal', symbol: '﷼', country: 'Saudi Arabia', isBase: false, status: 'active' },
  { id: '7', code: 'AED', name: 'UAE Dirham', symbol: 'د.إ', country: 'UAE', isBase: false, status: 'active' },
  { id: '8', code: 'MYR', name: 'Malaysian Ringgit', symbol: 'RM', country: 'Malaysia', isBase: false, status: 'active' },
];

const exchangeRates: ExchangeRate[] = [
  { id: '1', baseCurrency: 'USD', targetCurrency: 'BDT', rate: 110.50, inverseRate: 0.00905, effectiveDate: '2026-02-05', source: 'BB' },
  { id: '2', baseCurrency: 'EUR', targetCurrency: 'BDT', rate: 119.25, inverseRate: 0.00838, effectiveDate: '2026-02-05', source: 'BB' },
  { id: '3', baseCurrency: 'GBP', targetCurrency: 'BDT', rate: 138.50, inverseRate: 0.00722, effectiveDate: '2026-02-05', source: 'BB' },
  { id: '4', baseCurrency: 'INR', targetCurrency: 'BDT', rate: 1.32, inverseRate: 0.75758, effectiveDate: '2026-02-05', source: 'BB' },
  { id: '5', baseCurrency: 'SAR', targetCurrency: 'BDT', rate: 29.45, inverseRate: 0.03396, effectiveDate: '2026-02-05', source: 'Manual' },
  { id: '6', baseCurrency: 'AED', targetCurrency: 'BDT', rate: 30.10, inverseRate: 0.03322, effectiveDate: '2026-02-05', source: 'Manual' },
];

const rateHistory = [
  { date: 'Jan 31', usd: 109.80, eur: 118.50, gbp: 137.20 },
  { date: 'Feb 01', usd: 110.00, eur: 118.80, gbp: 137.80 },
  { date: 'Feb 02', usd: 110.25, eur: 119.00, gbp: 138.10 },
  { date: 'Feb 03', usd: 110.40, eur: 119.10, gbp: 138.30 },
  { date: 'Feb 04', usd: 110.35, eur: 119.20, gbp: 138.40 },
  { date: 'Feb 05', usd: 110.50, eur: 119.25, gbp: 138.50 },
];

const CurrencyManagement: React.FC = () => {
  const [showForm, setShowForm] = useState(false);
  const [selectedCurrency, setSelectedCurrency] = useState<Currency | null>(null);

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Currency Management</h1>
          <p className="text-gray-500">Multi-Currency & Exchange Rates</p>
        </div>
        <div className="flex gap-3">
          <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Update Rates
          </button>
          <button
            onClick={() => setShowForm(true)}
            className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            + Add Currency
          </button>
        </div>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Base Currency</p>
          <p className="text-2xl font-bold text-blue-600">BDT</p>
          <p className="text-xs text-gray-500">Bangladeshi Taka (৳)</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Active Currencies</p>
          <p className="text-2xl font-bold text-green-600">{currencies.length}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">USD Rate</p>
          <p className="text-2xl font-bold text-purple-600">৳110.50</p>
          <p className="text-xs text-green-500">↑ 0.65% this month</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Last Updated</p>
          <p className="text-2xl font-bold text-gray-600">Today</p>
          <p className="text-xs text-gray-500">10:00 AM</p>
        </div>
      </div>

      {/* Exchange Rate Trend */}
      <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 className="text-lg font-semibold mb-4">Exchange Rate Trend (BDT per 1 USD/EUR/GBP)</h3>
        <ResponsiveContainer width="100%" height={300}>
          <LineChart data={rateHistory}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="date" />
            <YAxis domain={['dataMin - 1', 'dataMax + 1']} />
            <Tooltip />
            <Line type="monotone" dataKey="usd" stroke="#3b82f6" strokeWidth={2} name="USD" />
            <Line type="monotone" dataKey="eur" stroke="#10b981" strokeWidth={2} name="EUR" />
            <Line type="monotone" dataKey="gbp" stroke="#f59e0b" strokeWidth={2} name="GBP" />
          </LineChart>
        </ResponsiveContainer>
      </div>

      {/* Currency Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div className="p-4 border-b border-gray-100">
          <h3 className="font-semibold text-gray-900">Currencies</h3>
        </div>
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Currency</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Symbol</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Country</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Base</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {currencies.map((currency) => (
              <tr key={currency.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 text-gray-900">{currency.name}</td>
                <td className="px-6 py-4 font-medium text-blue-600">{currency.code}</td>
                <td className="px-6 py-4 text-2xl">{currency.symbol}</td>
                <td className="px-6 py-4 text-gray-600">{currency.country}</td>
                <td className="px-6 py-4 text-center">
                  {currency.isBase && (
                    <span className="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                      Base
                    </span>
                  )}
                </td>
                <td className="px-6 py-4 text-center">
                  <span className={`px-2 py-1 text-xs font-medium rounded-full ${
                    currency.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                  }`}>
                    {currency.status}
                  </span>
                </td>
                <td className="px-6 py-4 text-center">
                  <button
                    onClick={() => setSelectedCurrency(currency)}
                    className="text-blue-600 hover:text-blue-800 mr-2"
                  >
                    Edit
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Exchange Rates Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div className="p-4 border-b border-gray-100 flex items-center justify-between">
          <h3 className="font-semibold text-gray-900">Exchange Rates</h3>
          <span className="text-sm text-gray-500">Base: BDT | Updated: Feb 05, 2026</span>
        </div>
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">From</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">To</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Rate</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Inverse</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Effective Date</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {exchangeRates.map((rate) => (
              <tr key={rate.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 font-medium text-gray-900">{rate.baseCurrency}</td>
                <td className="px-6 py-4 font-medium text-blue-600">{rate.targetCurrency}</td>
                <td className="px-6 py-4 text-right font-medium text-gray-900">৳{rate.rate.toFixed(2)}</td>
                <td className="px-6 py-4 text-right text-gray-600">{rate.inverseRate.toFixed(5)}</td>
                <td className="px-6 py-4 text-gray-600">{rate.effectiveDate}</td>
                <td className="px-6 py-4">
                  <span className={`px-2 py-1 text-xs font-medium rounded-full ${
                    rate.source === 'BB' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'
                  }`}>
                    {rate.source}
                  </span>
                </td>
                <td className="px-6 py-4 text-center">
                  <button className="text-blue-600 hover:text-blue-800">Update</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Currency Converter Widget */}
      <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 className="text-lg font-semibold mb-4">Currency Converter</h3>
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Amount</label>
            <input type="number" defaultValue={1000} className="w-full px-4 py-2 border border-gray-300 rounded-lg" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">From</label>
            <select defaultValue="USD" className="w-full px-4 py-2 border border-gray-300 rounded-lg">
              {currencies.map(c => (
                <option key={c.code} value={c.code}>{c.code} - {c.name}</option>
              ))}
            </select>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">To</label>
            <select defaultValue="BDT" className="w-full px-4 py-2 border border-gray-300 rounded-lg">
              {currencies.map(c => (
                <option key={c.code} value={c.code}>{c.code} - {c.name}</option>
              ))}
            </select>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Result</label>
            <div className="px-4 py-2 bg-gray-100 rounded-lg font-bold text-xl text-blue-600">
              ৳110,500.00
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CurrencyManagement;
