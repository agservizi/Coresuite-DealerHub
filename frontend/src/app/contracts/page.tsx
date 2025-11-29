"use client";

import { Download, LineChart } from "lucide-react";
import { useEffect, useState } from "react";
import { AppShell } from "@/components/layout/AppShell";
import { PageHeader } from "@/components/shared/PageHeader";
import { QuickActionCard } from "@/components/shared/QuickActionCard";
import { fetchContracts } from "@/lib/api";
import { useAuth } from "@/context/AuthContext";
import type { ContractSummary } from "@/types";
import Link from "next/link";

export default function ContractsPage() {
  const { token, user } = useAuth();
  const [contracts, setContracts] = useState<ContractSummary[]>([]);

  useEffect(() => {
    if (!token) return;
    fetchContracts(token).then(setContracts).catch(console.error);
  }, [token]);

  return (
    <AppShell>
      <PageHeader
        title="Contratti"
        description={
          user?.role === "AFFILIATO"
            ? "Visualizza i contratti caricati da te"
            : "Gestisci e monitora tutti i contratti"
        }
        cta={
          <Link
            href="/contracts/new"
            className="rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-white"
          >
            Nuovo contratto
          </Link>
        }
      />

      <div className="glass-card overflow-hidden">
        <div className="grid grid-cols-6 bg-gray-50 px-4 py-2 text-xs uppercase text-gray-500">
          <span>Cliente</span>
          <span>Servizio</span>
          <span>Gestore</span>
          <span>Stato</span>
          <span>Affiliate</span>
          <span>Ultimo update</span>
        </div>
        <div className="divide-y divide-white/5">
          {contracts.map((contract) => (
            <div key={contract.id} className="grid grid-cols-6 items-center px-4 py-3 text-sm">
              <span className="font-medium text-white">{contract.customerName}</span>
              <span>{contract.serviceType}</span>
              <span>{contract.provider}</span>
              <span>
                <span className="rounded-full bg-white/10 px-3 py-1 text-xs">
                  {contract.status}
                </span>
              </span>
              <span>{contract.affiliateName}</span>
              <span className="text-xs text-gray-400">
                {new Date(contract.updatedAt).toLocaleDateString("it-IT")}
              </span>
            </div>
          ))}
          {!contracts.length && (
            <p className="px-4 py-6 text-center text-sm text-gray-400">
              Nessun contratto presente.
            </p>
          )}
        </div>
      </div>

      {user?.role === "SUPERADMIN" && (
        <div className="mt-8 grid gap-4 md:grid-cols-2">
          <QuickActionCard
            title="Esporta contratti"
            description="Scarica report CSV/PDF"
            href="/contracts/export"
            icon={<Download size={20} />}
          />
          <QuickActionCard
            title="Statistiche affiliati"
            description="Visualizza performance"
            href="/stats"
            icon={<LineChart size={20} />}
          />
        </div>
      )}
    </AppShell>
  );
}
