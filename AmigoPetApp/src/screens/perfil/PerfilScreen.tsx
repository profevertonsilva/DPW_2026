import React, { useEffect, useState } from 'react';
import { View, Text, ScrollView, StyleSheet, TouchableOpacity, ActivityIndicator } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { PerfilStackParamList } from '../../navigation/stacks/PerfilStack';
import { Avatar } from '../../components/ui/Avatar';
import { RankingBadge } from '../../components/domain/RankingBadge';
import { adotanteService } from '../../api/services/adotanteService';
import { ongService } from '../../api/services/ongService';
import { vetService } from '../../api/services/vetService';
import { useAuth } from '../../hooks/useAuth';
import type { RankingAdotante } from '../../types/Adotante';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';

type Props = NativeStackScreenProps<PerfilStackParamList, 'Perfil'>;

interface PerfilResumo {
  foto: string | null;
  ranking?: RankingAdotante;
  cnpj?: string;
  crmv?: string;
}

export function PerfilScreen({ navigation }: Props) {
  const { user, logout } = useAuth();
  const [perfil, setPerfil] = useState<PerfilResumo | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function load() {
      try {
        if (user?.tipo_usuario === 'adotante') {
          const data = await adotanteService.perfil();
          setPerfil({ foto: data.foto ?? null, ranking: data.ranking });
        } else if (user?.tipo_usuario === 'ong') {
          const data = await ongService.perfil();
          setPerfil({ foto: data.foto ?? null, cnpj: data.cnpj });
        } else if (user?.tipo_usuario === 'veterinario') {
          const data = await vetService.perfil();
          setPerfil({ foto: data.foto ?? null, crmv: data.crmv });
        } else {
          setPerfil({ foto: null });
        }
      } catch {
        setPerfil({ foto: null });
      } finally {
        setLoading(false);
      }
    }
    load();
  }, [user?.tipo_usuario]);

  function navegarEditar() {
    if (user?.tipo_usuario === 'ong') navigation.navigate('EditarPerfilOng');
    else if (user?.tipo_usuario === 'veterinario') navigation.navigate('EditarPerfilVet');
    else navigation.navigate('EditarPerfil');
  }

  const isWebAdmin = user?.tipo_usuario === 'administrador' || user?.tipo_usuario === 'moderador';

  if (loading) {
    return <View style={styles.center}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  return (
    <ScrollView style={styles.root} contentContainerStyle={styles.scroll}>
      {isWebAdmin && (
        <View style={styles.adminBanner}>
          <Text style={styles.adminBannerTitle}>⚙️ Conta {user?.tipo_usuario}</Text>
          <Text style={styles.adminBannerText}>
            Funções de moderação (aprovação de conteúdo, gerenciamento de usuários, remoção de publicações)
            estão disponíveis exclusivamente no painel web.{'\n'}
            Acesse: <Text style={styles.adminBannerLink}>amigopet.com/admin</Text>
          </Text>
        </View>
      )}

      <View style={styles.card}>
        <Avatar uri={perfil?.foto ?? null} nome={user?.nome} size={80} />
        <Text style={styles.nome}>{user?.nome}</Text>
        <Text style={styles.email}>{user?.email}</Text>
        {perfil?.ranking && (
          <View style={styles.rankingRow}>
            <RankingBadge ranking={perfil.ranking} />
          </View>
        )}
        {perfil?.cnpj && (
          <Text style={styles.badge}>CNPJ: {perfil.cnpj}</Text>
        )}
        {perfil?.crmv && (
          <Text style={styles.badge}>CRMV: {perfil.crmv}</Text>
        )}
      </View>

      <View style={styles.menu}>
        {!isWebAdmin && (
          <MenuItem label="Editar Perfil" icon="✏️" onPress={navegarEditar} />
        )}
        {user?.tipo_usuario === 'adotante' && (
          <MenuItem label="Histórico de Adoções" icon="📋" onPress={() => navigation.navigate('Historico')} />
        )}
        <MenuItem label="Notificações" icon="🔔" onPress={() => navigation.navigate('Notificacoes')} />
      </View>

      <TouchableOpacity style={styles.btnSair} onPress={logout} activeOpacity={0.85}>
        <Text style={styles.btnSairLabel}>Sair da conta</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

function MenuItem({ label, icon, onPress }: { label: string; icon: string; onPress: () => void }) {
  return (
    <TouchableOpacity style={styles.menuItem} onPress={onPress} activeOpacity={0.8}>
      <Text style={styles.menuIcon}>{icon}</Text>
      <Text style={styles.menuLabel}>{label}</Text>
      <Text style={styles.seta}>›</Text>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.bgMuted },
  scroll: { padding: spacing.md },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  card: { alignItems: 'center', backgroundColor: colors.bg, borderRadius: 12, padding: spacing.lg, marginBottom: spacing.md, elevation: 2, shadowColor: colors.black, shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.06, shadowRadius: 6 },
  nome: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.xl, color: colors.text, marginTop: spacing.sm },
  email: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary, marginTop: 2 },
  rankingRow: { marginTop: spacing.sm },
  badge: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary, marginTop: spacing.xs },
  menu: { backgroundColor: colors.bg, borderRadius: 12, marginBottom: spacing.md, overflow: 'hidden', elevation: 2, shadowColor: colors.black, shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.06, shadowRadius: 6 },
  menuItem: { flexDirection: 'row', alignItems: 'center', padding: spacing.md, borderBottomWidth: 1, borderBottomColor: colors.border },
  menuIcon: { fontSize: 20, marginRight: spacing.md },
  menuLabel: { flex: 1, fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.text },
  seta: { fontSize: 20, color: colors.secondary },
  btnSair: { backgroundColor: colors.bg, borderRadius: 12, padding: spacing.md, alignItems: 'center', borderWidth: 1, borderColor: colors.error },
  btnSairLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.error },
  adminBanner: { backgroundColor: '#FFF3CD', borderRadius: 10, padding: spacing.md, marginBottom: spacing.md, borderLeftWidth: 3, borderLeftColor: colors.accent },
  adminBannerTitle: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: '#856404', marginBottom: 4 },
  adminBannerText: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: '#856404', lineHeight: 20 },
  adminBannerLink: { fontFamily: typography.fontFamily.bodyBold, color: '#856404', textDecorationLine: 'underline' },
});
