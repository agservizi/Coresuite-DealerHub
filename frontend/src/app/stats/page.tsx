"use client";

import { AppShell } from "@/components/layout/AppShell";
import { DashboardCharts } from "@/components/dashboard/Charts";
import { PageHeader } from "@/components/shared/PageHeader";
import { useAuthGuard } from "@/hooks/useAuthGuard";

export const metadata = {
  title: "DealerHub | Statistiche",
};

export default function StatsPage() {
  useAuthGuard({ allowedRoles: ["SUPERADMIN"] });

  return (
    <AppShell>
      <PageHeader
        title="Statistiche"
        description="Andamento contratti per data, gestore e servizio"
      />
      <DashboardCharts
        bars={{ label: "Per gestore", data: [45, 32, 28, 12], color: "#7c3aed" }}
        lines={{ label: "Totale mensile", data: [50, 62, 71, 65, 78], color: "#22c55e" }}
        donut={{ label: "Tipologie", data: [40, 30, 20, 10], color: "#0ea5e9" }}
      />
    </AppShell>
  );
}
