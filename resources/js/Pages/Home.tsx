import { Head } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';

export default function Home() {
    return <PublicLayout>
        <Head title="Mutoko Rural District Council" />
        <section className="intro"><div className="container"><p className="eyebrow">Official council website</p><h1>Mutoko Rural District Council</h1><p className="lead">Service Delivery for Sustainable Communities</p></div></section>
        <section className="container home-content" aria-labelledby="information-heading"><div><h2 id="information-heading">Council information and services</h2><p>This website is being prepared to provide access to approved council information, services and public notices.</p></div><aside><h2>Public information</h2><p>Content will appear here when it has been reviewed and published by the council.</p></aside></section>
    </PublicLayout>;
}
