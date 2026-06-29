import React, { useState } from 'react';
import {
  View,
  StyleSheet,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  TouchableOpacity,
} from 'react-native';
import { Text, TextInput, Button, Snackbar } from 'react-native-paper';
import { useForm, Controller } from 'react-hook-form';
import { z } from 'zod';
import { zodResolver } from '@hookform/resolvers/zod';
import { useNavigation } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useAuth } from '../../hooks/useAuth';
import { colors } from '../../theme/colors';
import { spacing } from '../../theme/spacing';
import { typography } from '../../theme/typography';
import type { AuthStackParamList } from '../../navigation/AuthStack';

const schema = z.object({
  email: z.string().email('E-mail inválido'),
  senha: z.string().min(6, 'Senha deve ter ao menos 6 caracteres'),
});

type FormData = z.infer<typeof schema>;

export function LoginScreen() {
  const { login } = useAuth();
  const navigation = useNavigation<NativeStackNavigationProp<AuthStackParamList>>();
  const [isLoading, setIsLoading] = useState(false);
  const [senhaVisivel, setSenhaVisivel] = useState(false);
  const [snackMsg, setSnackMsg] = useState('');
  const [snackVisible, setSnackVisible] = useState(false);

  const {
    control,
    handleSubmit,
    formState: { errors },
  } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: { email: '', senha: '' },
  });

  const onSubmit = async (data: FormData) => {
    setIsLoading(true);
    try {
      await login({ email: data.email, senha: data.senha });
      // RootNavigator detecta user != null e muda para AppTabs automaticamente
    } catch (err: any) {
      let msg: string;
      if (err?.response?.data?.erro) {
        msg = err.response.data.erro;
      } else if (err?.code === 'ERR_NETWORK' || err?.message === 'Network Error') {
        msg = 'Sem conexão com o servidor. Verifique a URL da API e sua rede.';
      } else {
        msg = 'Erro ao tentar login. Tente novamente.';
      }
      setSnackMsg(msg);
      setSnackVisible(true);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.flex}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <ScrollView contentContainerStyle={styles.container} keyboardShouldPersistTaps="handled">
        {/* Header */}
        <View style={styles.header}>
          <Text style={styles.logo}>🐾</Text>
          <Text style={styles.titulo}>AmigoPet</Text>
          <Text style={styles.subtitulo}>Adote um amigo, transforme uma vida.</Text>
        </View>

        {/* Form */}
        <View style={styles.form}>
          <Controller
            control={control}
            name="email"
            render={({ field: { onChange, onBlur, value } }) => (
              <TextInput
                label="E-mail"
                mode="outlined"
                keyboardType="email-address"
                autoCapitalize="none"
                value={value}
                onBlur={onBlur}
                onChangeText={onChange}
                error={!!errors.email}
                style={styles.input}
                outlineColor={colors.border}
                activeOutlineColor={colors.primary}
              />
            )}
          />
          {errors.email && (
            <Text style={styles.errorText}>{errors.email.message}</Text>
          )}

          <Controller
            control={control}
            name="senha"
            render={({ field: { onChange, onBlur, value } }) => (
              <TextInput
                label="Senha"
                mode="outlined"
                secureTextEntry={!senhaVisivel}
                value={value}
                onBlur={onBlur}
                onChangeText={onChange}
                error={!!errors.senha}
                style={styles.input}
                outlineColor={colors.border}
                activeOutlineColor={colors.primary}
                right={
                  <TextInput.Icon
                    icon={senhaVisivel ? 'eye-off' : 'eye'}
                    onPress={() => setSenhaVisivel(v => !v)}
                  />
                }
              />
            )}
          />
          {errors.senha && (
            <Text style={styles.errorText}>{errors.senha.message}</Text>
          )}

          <Button
            mode="contained"
            onPress={handleSubmit(onSubmit)}
            loading={isLoading}
            disabled={isLoading}
            style={styles.btnEntrar}
            contentStyle={styles.btnContent}
            buttonColor={colors.primary}
            labelStyle={styles.btnLabel}
          >
            Entrar
          </Button>
        </View>

        {/* Esqueci a senha */}
        <TouchableOpacity
          style={styles.linkEsqueciRow}
          onPress={() => navigation.navigate('RecuperarSenha')}
        >
          <Text style={styles.linkEsqueci}>Esqueci minha senha</Text>
        </TouchableOpacity>

        {/* Rodapé */}
        <View style={styles.rodape}>
          <Text style={styles.rodapeTexto}>Não tem conta? </Text>
          <TouchableOpacity onPress={() => navigation.navigate('EscolhaCadastro')}>
            <Text style={styles.linkCadastro}>Cadastre-se</Text>
          </TouchableOpacity>
        </View>

        {/* Credenciais de teste */}
        <View style={styles.hintBox}>
          <Text style={styles.hint}>
            [Teste] adotante.teste@amigopet.com · ong.teste@amigopet.com · vet.teste@amigopet.com{'\n'}
            Senha: Teste123!
          </Text>
        </View>
      </ScrollView>

      <Snackbar
        visible={snackVisible}
        onDismiss={() => setSnackVisible(false)}
        duration={3500}
        style={{ backgroundColor: colors.error }}
      >
        {snackMsg}
      </Snackbar>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  flex: { flex: 1, backgroundColor: colors.bg },
  container: {
    flexGrow: 1,
    justifyContent: 'center',
    paddingHorizontal: spacing.lg,
    paddingVertical: spacing.xxl,
  },
  header: { alignItems: 'center', marginBottom: spacing.xl },
  logo: { fontSize: 64 },
  titulo: {
    fontFamily: typography.fontFamily.titleBold,
    fontSize: typography.fontSize.display,
    color: colors.primary,
    marginTop: spacing.sm,
  },
  subtitulo: {
    fontFamily: typography.fontFamily.body,
    fontSize: typography.fontSize.md,
    color: colors.text,
    marginTop: spacing.xs,
    textAlign: 'center',
  },
  form: { gap: spacing.xs },
  input: { backgroundColor: colors.bg },
  errorText: {
    fontFamily: typography.fontFamily.body,
    fontSize: typography.fontSize.sm,
    color: colors.error,
    marginLeft: spacing.xs,
    marginTop: -spacing.xs,
  },
  btnEntrar: {
    marginTop: spacing.md,
    borderRadius: 8,
  },
  btnContent: { height: 50 },
  btnLabel: {
    fontFamily: typography.fontFamily.titleBold,
    fontSize: typography.fontSize.md,
    letterSpacing: 0.5,
  },
  linkEsqueciRow: { alignItems: 'flex-end', marginTop: spacing.xs, marginBottom: spacing.sm },
  linkEsqueci: {
    fontFamily: typography.fontFamily.body,
    color: colors.primary,
    fontSize: typography.fontSize.sm,
  },
  rodape: {
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: spacing.lg,
  },
  rodapeTexto: {
    fontFamily: typography.fontFamily.body,
    color: colors.text,
    fontSize: typography.fontSize.md,
  },
  linkCadastro: {
    fontFamily: typography.fontFamily.bodyBold,
    color: colors.primary,
    fontSize: typography.fontSize.md,
  },
  hintBox: {
    marginTop: spacing.xl,
    backgroundColor: colors.bgMuted,
    borderRadius: 10,
    paddingVertical: spacing.sm,
    paddingHorizontal: spacing.md,
  },
  hint: {
    fontFamily: typography.fontFamily.body,
    fontSize: typography.fontSize.xs,
    color: colors.secondary,
    textAlign: 'center',
    lineHeight: 18,
  },
});
