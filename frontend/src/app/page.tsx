import Link from "next/link";

export default function Home() {
  return (
    <main className="flex min-h-screen flex-col items-center justify-center bg-slate-950 px-4 text-center text-white">
      <p className="text-sm uppercase tracking-[0.3em] text-slate-400">DealerHub</p>
      <h1 className="mt-2 text-4xl font-semibold">Portale contratti</h1>
      <p className="mt-4 max-w-2xl text-slate-400">
        Accedi all&apos;area riservata per gestire contratti Telefonia, Luce e Gas. Dashboard,
        caricamento documenti e statistiche sempre disponibili.
      </p>
      <Link
        href="/login"
        className="mt-8 rounded-full bg-emerald-500 px-6 py-3 font-semibold text-slate-900"
      >
        Vai al login
      </Link>
    </main>
  );
}
