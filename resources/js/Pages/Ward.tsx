import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation } from '../usePublicTranslation';
type WardContent = { name: string; description: string | null; boundaries_description: string | null };
export default function Ward({ ward }: { ward: WardContent }) {
    const t = usePublicTranslation();
    return <PublicLayout><div className="container coming-soon"><h1>{ward.name}</h1>{ward.description && <p>{ward.description}</p>}{ward.boundaries_description && <section><h2>{t('areaDescription')}</h2><p>{ward.boundaries_description}</p></section>}</div></PublicLayout>;
}
