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
}

export default function AffiliatesPage() {
  useAuthGuard({ allowedRoles: ["SUPERADMIN"] });
  const { token } = useAuth();
  const [affiliates, setAffiliates] = useState<Affiliate[]>([]);

  useEffect(() => {
    if (!token) return;
    fetchAffiliates(token)
      .then((data) => setAffiliates(data as Affiliate[]))
      .catch(console.error);
  }, [token]);

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

  return (
    <AppShell>
      <PageHeader
        title="Affiliati"
        description="Gestisci accessi, reset password e stato attivazione"
      />
      <div className="glass-card overflow-hidden">
        <div className="grid grid-cols-5 bg-white/5 px-4 py-2 text-xs uppercase text-slate-400">
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
                <button className="rounded-full border border-white/10 px-3 py-1 text-xs">
                  Reset password
                </button>
              </div>
            </div>
          ))}
          {!affiliates.length && (
            <p className="px-4 py-6 text-center text-sm text-slate-400">
              Nessun affiliato disponibile.
            </p>
          )}
        </div>
      </div>
    </AppShell>
  );
}
