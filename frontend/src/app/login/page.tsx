import { LoginForm } from "@/components/forms/LoginForm";
import Link from "next/link";

export const metadata = {
  title: "DealerHub | Login",
};

export default function LoginPage() {
  return (
    <main className="flex min-h-screen flex-col items-center justify-center bg-slate-950 px-4">
      <div className="w-full max-w-md space-y-6 rounded-3xl border border-white/10 bg-white/5 p-8 text-white shadow-2xl">
        <div className="text-center">
          <p className="text-xs uppercase tracking-[0.3em] text-slate-400">DealerHub</p>
          <h1 className="mt-2 text-2xl font-semibold">Accedi al portale</h1>
        </div>
        <LoginForm />
        <p className="text-center text-xs text-slate-400">
          Problemi di accesso? <Link href="/forgot-password" className="text-emerald-300">Recupera qui</Link>
        </p>
      </div>
    </main>
  );
}
