import { RecoverForm } from "@/components/forms/RecoverForm";
import Link from "next/link";

export const metadata = {
  title: "DealerHub | Recupero password",
};

export default function ForgotPasswordPage() {
  return (
    <main className="flex min-h-screen flex-col items-center justify-center bg-slate-950 px-4">
      <div className="w-full max-w-md space-y-6 rounded-3xl border border-white/10 bg-white/5 p-8 text-white shadow-2xl">
        <div className="text-center">
          <p className="text-xs uppercase tracking-[0.3em] text-slate-400">DealerHub</p>
          <h1 className="mt-2 text-2xl font-semibold">Recupera password</h1>
          <p className="text-sm text-slate-400">
            Inserisci l&apos;email collegata al tuo account. Riceverai un link temporaneo.
          </p>
        </div>
        <RecoverForm />
        <p className="text-center text-xs text-slate-400">
          <Link href="/login" className="text-emerald-300">
            Torna al login
          </Link>
        </p>
      </div>
    </main>
  );
}
