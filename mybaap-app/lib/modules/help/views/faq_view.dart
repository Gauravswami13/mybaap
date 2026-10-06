import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/state_message.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/help/providers/content_provider.dart';
import 'package:provider/provider.dart';

class FaqView extends StatefulWidget {
  const FaqView({super.key});

  @override
  State<FaqView> createState() => _FaqViewState();
}

class _FaqViewState extends State<FaqView> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<ContentProvider>().loadFaqs();
    });
  }

  @override
  Widget build(BuildContext context) {
    final state = context.watch<ContentProvider>();
    return Scaffold(
      backgroundColor: ColorConstant.bgCl,
      appBar: AppBar(title: const Text('FAQ', style: TextStyle(fontFamily: FontsStyle.semiBold))),
      body: state.loading
          ? const Center(child: CircularProgressIndicator(color: ColorConstant.primaryCl))
          : state.error != null
              ? StateMessage(title: 'Could not load FAQs', subtitle: state.error, onRetry: state.loadFaqs)
              : state.faqs.isEmpty
                  ? StateMessage(title: 'No questions yet', onRetry: state.loadFaqs)
                  : ListView(
                      children: state.faqs
                          .map(
                            (item) => ExpansionTile(
                              title: Text(item.question, style: const TextStyle(fontFamily: FontsStyle.medium)),
                              children: [
                                Padding(
                                  padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
                                  child: Align(
                                    alignment: Alignment.centerLeft,
                                    child: Text(item.answer, style: const TextStyle(color: ColorConstant.bodyTextCl)),
                                  ),
                                ),
                              ],
                            ),
                          )
                          .toList(),
                    ),
    );
  }
}
