"use client";

import { useEffect, useState } from "react";
import { AreaChart, ClipboardList, FilePlus, ShieldCheck } from "lucide-react";
import { AppShell } from "@/components/layout/AppShell";
import { StatCard } from "@/components/shared/StatCard";
import { QuickActionCard } from "@/components/shared/QuickActionCard";
import { CoverageLinks } from "@/components/dashboard/CoverageLinks";
import { DashboardCharts } from "@/components/dashboard/Charts";
import { ContractsTable } from "@/components/dashboard/ContractsTable";
import { fetchContracts, fetchDashboardStats } from "@/lib/api";
import { useAuth } from "@/context/AuthContext";
import type { ContractSummary, DashboardStats } from "@/types";

export default function DashboardPage() {
  const { token, user } = useAuth();
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [contracts, setContracts] = useState<ContractSummary[]>([]);

  useEffect(() => {
    if (!token) return;
    fetchDashboardStats(token).then(setStats).catch(console.error);
    fetchContracts(token).then(setContracts).catch(console.error);
  }, [token]);

  return (
    <AppShell>
      <div className="space-y-8">
        <section className="dashboard-grid">
          <StatCard
            label="Contratti totali"
            value={stats?.totalContracts ?? "--"}
            icon={<ClipboardList size={18} />}
            highlight
          />
          <StatCard
            label="In lavorazione"
            value={stats?.pendingContracts ?? "--"}
            icon={<FilePlus size={18} />}
          />
          <StatCard
            label="Affiliati attivi"
            value={stats?.affiliatesEnabled ?? "--"}
            icon={<ShieldCheck size={18} />}
          />
          <StatCard
            label="Controlli copertura"
            value={stats?.coverageChecks ?? "--"}
            icon={<AreaChart size={18} />}
          />
        </section>

        <section className="grid gap-6 lg:grid-cols-3">
          <QuickActionCard
            title="Carica contratto"
            description="Upload documenti e stato workflow"
            href="/contracts/new"
            icon={<FilePlus size={20} />}
          />
          <QuickActionCard
            title="Gestisci contratti"
            description="Filtra per gestore e servizio"
            href="/contracts"
            icon={<ClipboardList size={20} />}
          />
          <QuickActionCard
            title="Statistiche"
            description="Andamento commerciale"
            href="/stats"
            icon={<AreaChart size={20} />}
          />
        </section>

        {user?.role === "AFFILIATO" && <CoverageLinks />}

        <ContractsTable contracts={contracts.slice(0, 5)} />

        <DashboardCharts
          bars={{ label: "Gestori", data: [12, 8, 6, 4], color: "#22d3ee" }}
          lines={{ label: "Contratti", data: [5, 15, 8, 12], color: "#22c55e" }}
          donut={{ label: "Servizi", data: [25, 18, 10, 6], color: "#818cf8" }}
        />
      </div>
    </AppShell>
  );
}
