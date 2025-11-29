import { AppShell } from "@/components/layout/AppShell";
import { ContractForm } from "@/components/forms/ContractForm";
import { PageHeader } from "@/components/shared/PageHeader";

export const metadata = {
  title: "DealerHub | Nuovo contratto",
};

export default function NewContractPage() {
  return (
    <AppShell>
      <PageHeader
        title="Nuovo contratto"
        description="Inserisci i dati del cliente e carica i documenti richiesti"
      />
      <div className="glass-card p-6">
        <ContractForm />
      </div>
    </AppShell>
  );
}
