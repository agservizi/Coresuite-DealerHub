"use client";

import { useEffect, useState } from "react";
import { AppShell } from "@/components/layout/AppShell";
import { PageHeader } from "@/components/shared/PageHeader";
import { useAuthGuard } from "@/hooks/useAuthGuard";
import { manageAffiliate, fetchAffiliates } from "@/lib/api";
import { useAuth } from "@/context/AuthContext";

interface Affiliate {
  id: number;
  name: string;
  email: string;
  contracts: number;
  active: boolean;
  role: string;
}

export default function AffiliatesPage() {
  useAuthGuard({ allowedRoles: ["SUPERADMIN"] });
  const { token } = useAuth();
  const [affiliates, setAffiliates] = useState<Affiliate[]>([]);
  const [showCreateForm, setShowCreateForm] = useState(false);
  const [newAffiliate, setNewAffiliate] = useState({ name: '', email: '', password: 'Dealer123' });

  useEffect(() => {
    if (!token) return;
    fetchAffiliates(token)
      .then((data) => setAffiliates((data as Affiliate[]).filter(u => u.role === 'AFFILIATO')))
      .catch(console.error);
  }, [token]);

  const createAffiliate = async () => {
    if (!token || !newAffiliate.name || !newAffiliate.email) return;
    await manageAffiliate(token, {
      action: 'create',
      name: newAffiliate.name,
      email: newAffiliate.email,
      password: newAffiliate.password,
      role: 'AFFILIATO',
    });
    setNewAffiliate({ name: '', email: '', password: 'Dealer123' });
    setShowCreateForm(false);
    // Ricarica la lista
    fetchAffiliates(token)
      .then((data) => setAffiliates((data as Affiliate[]).filter(u => u.role === 'AFFILIATO')))
      .catch(console.error);
  };

  const toggleAffiliate = async (affiliate: Affiliate) => {
    if (!token) return;
    await manageAffiliate(token, {
      action: affiliate.active ? "disable" : "enable",
      userId: affiliate.id,
    });
    setAffiliates((prev) =>
      prev.map((item) =>
        item.id === affiliate.id ? { ...item, active: !item.active } : item
      )
    );
  };

  const resetPassword = async (affiliate: Affiliate) => {
    if (!token) return;
    const newPassword = prompt(`Nuova password per ${affiliate.name}:`, 'Dealer123');
    if (!newPassword) return;
    await manageAffiliate(token, {
      action: 'reset-password',
      userId: affiliate.id,
      newPassword,
    });
    alert('Password resettata con successo');
  };

  return (
    <AppShell>
      <PageHeader
        title="Affiliati"
        description="Gestisci accessi, reset password e stato attivazione"
      />
      <div className="mb-4 flex justify-end">
        <button
          className="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white"
          onClick={() => setShowCreateForm(!showCreateForm)}
        >
          {showCreateForm ? 'Annulla' : 'Crea Affiliato'}
        </button>
      </div>
      {showCreateForm && (
        <div className="glass-card mb-4 p-4">
          <h3 className="mb-2 text-lg font-semibold">Crea Nuovo Affiliato</h3>
          <div className="grid gap-4 md:grid-cols-3">
            <input
              type="text"
              placeholder="Nome"
              value={newAffiliate.name}
              onChange={(e) => setNewAffiliate({ ...newAffiliate, name: e.target.value })}
              className="rounded-lg border border-white/10 bg-white/5 p-2 text-white"
            />
            <input
              type="email"
              placeholder="Email"
              value={newAffiliate.email}
              onChange={(e) => setNewAffiliate({ ...newAffiliate, email: e.target.value })}
              className="rounded-lg border border-white/10 bg-white/5 p-2 text-white"
            />
            <input
              type="password"
              placeholder="Password"
              value={newAffiliate.password}
              onChange={(e) => setNewAffiliate({ ...newAffiliate, password: e.target.value })}
              className="rounded-lg border border-white/10 bg-white/5 p-2 text-white"
            />
          </div>
          <button
            className="mt-4 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white"
            onClick={createAffiliate}
          >
            Crea
          </button>
        </div>
      )}
      <div className="glass-card overflow-hidden">
        <div className="grid grid-cols-5 bg-white/5 px-4 py-2 text-xs uppercase text-gray-400">
          <span>Nome</span>
          <span>Email</span>
          <span>Contratti</span>
          <span>Stato</span>
          <span>Azioni</span>
        </div>
        <div className="divide-y divide-white/5 text-sm">
          {affiliates.map((affiliate) => (
            <div key={affiliate.id} className="grid grid-cols-5 items-center px-4 py-3">
              <span className="font-medium text-white">{affiliate.name}</span>
              <span>{affiliate.email}</span>
              <span>{affiliate.contracts}</span>
              <span className={affiliate.active ? "text-emerald-300" : "text-rose-300"}>
                {affiliate.active ? "Attivo" : "Disattivato"}
              </span>
              <div className="flex gap-2">
                <button
                  className="rounded-full border border-white/10 px-3 py-1 text-xs"
                  onClick={() => toggleAffiliate(affiliate)}
                >
                  {affiliate.active ? "Disattiva" : "Attiva"}
                </button>
                <button
                  className="rounded-full border border-white/10 px-3 py-1 text-xs"
                  onClick={() => resetPassword(affiliate)}
                >
                  Reset password
                </button>
              </div>
            </div>
          ))}
          {!affiliates.length && (
            <p className="px-4 py-6 text-center text-sm text-gray-400">
              Nessun affiliato disponibile.
            </p>
          )}
        </div>
      </div>
    </AppShell>
  );
}
