import { AppShell } from "@/components/layout/AppShell";
import { PageHeader } from "@/components/shared/PageHeader";

export const metadata = {
  title: "DealerHub | Esporta contratti",
};

export default function ContractsExportPage() {
  return (
    <AppShell>
      <PageHeader
        title="Esporta contratti"
        description="Scegli intervallo date e formato di esportazione"
      />
      <div className="glass-card max-w-2xl space-y-4 p-6">
        <label className="text-sm">
          Dal
          <input type="date" className="mt-1 w-full rounded-lg border border-white/10 bg-white/5 p-2" />
        </label>
        <label className="text-sm">
          Al
          <input type="date" className="mt-1 w-full rounded-lg border border-white/10 bg-white/5 p-2" />
        </label>
        <label className="text-sm">
          Formato
          <select className="mt-1 w-full rounded-lg border border-white/10 bg-white/5 p-2">
            <option value="csv">CSV</option>
            <option value="pdf">PDF</option>
          </select>
        </label>
        <button className="w-full rounded-xl bg-emerald-500 py-3 font-semibold text-white">
          Esporta
        </button>
      </div>
    </AppShell>
  );
}
