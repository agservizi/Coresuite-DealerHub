import { LoginForm } from "@/components/forms/LoginForm";
import Link from "next/link";

export const metadata = {
  title: "DealerHub | Login",
};

export default function LoginPage() {
  return (
    <main className="min-h-screen bg-gray-100">
      <div className="grid min-h-screen md:grid-cols-2">
        {/* Colonna sinistra - visibile solo su desktop */}
        <div className="hidden md:flex flex-col items-center justify-center bg-white px-8 text-center text-gray-900">
          <div className="max-w-md">
            <h2 className="text-4xl font-bold mb-4">DealerHub</h2>
            <p className="text-lg text-gray-600">
              Gestisci i tuoi contratti di telefonia, luce e gas in modo semplice e sicuro.
            </p>
          </div>
        </div>

        {/* Colonna destra - form di login */}
        <div className="flex flex-col items-center justify-center px-4 py-8 md:px-8">
          <div className="w-full max-w-md space-y-6 text-gray-900">
            <div className="text-center">
              <p className="text-xs uppercase tracking-[0.3em] text-gray-500">DealerHub</p>
              <h1 className="mt-2 text-2xl font-semibold md:text-3xl">Accedi al portale</h1>
            </div>
            <LoginForm />
            <p className="text-center text-xs text-gray-500">
              Problemi di accesso? <Link href="/forgot-password" className="text-emerald-600 hover:underline">Recupera qui</Link>
            </p>
          </div>
        </div>
      </div>
    </main>
  );
}
