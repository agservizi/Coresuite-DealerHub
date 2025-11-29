"use client";

import { AppShell } from "@/components/layout/AppShell";
import { CoverageForm } from "@/components/forms/CoverageForm";
import { PageHeader } from "@/components/shared/PageHeader";
import { useAuthGuard } from "@/hooks/useAuthGuard";

export const metadata = {
  title: "DealerHub | Controllo copertura",
};

export default function CoveragePage() {
  useAuthGuard({ allowedRoles: ["AFFILIATO"] });

  return (
    <AppShell>
      <PageHeader
        title="Controllo copertura"
        description="Esegui verifiche per i principali operatori"
      />
      <CoverageForm />
    </AppShell>
  );
}
