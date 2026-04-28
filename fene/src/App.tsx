/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import { BrowserRouter, Routes, Route } from "react-router-dom";
import RootLayout from "./layouts/RootLayout";
import Home from "./pages/Home";
import ServiceListing from "./pages/ServiceListing";
import ServiceDetail from "./pages/ServiceDetail";
import CustomerDashboard from "./pages/CustomerDashboard";
import AITripPlanner from "./pages/AITripPlanner";
import ProviderDashboard from "./pages/ProviderDashboard";
import ManageServices from "./pages/ManageServices";
import ManageBookings from "./pages/ManageBookings";

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<RootLayout />}>
          <Route index element={<Home />} />
          <Route path="services" element={<ServiceListing />} />
          <Route path="services/:id" element={<ServiceDetail />} />
          <Route path="customer/dashboard" element={<CustomerDashboard />} />
          <Route path="ai-planner" element={<AITripPlanner />} />
          <Route path="provider/dashboard" element={<ProviderDashboard />} />
          <Route path="provider/services" element={<ManageServices />} />
          <Route path="provider/bookings" element={<ManageBookings />} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}
