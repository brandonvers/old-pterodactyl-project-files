import React, { useEffect, useState } from 'react';
import { RouteComponentProps } from "react-router-dom";
import PageContentBlock from '@/components/elements/PageContentBlock';
import useFlash from '@/plugins/useFlash';
import tw from 'twin.macro';
import useSWR from 'swr';
import Spinner from '@/components/elements/Spinner';
import GreyRowBox from '@/components/elements/GreyRowBox';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faBook } from '@fortawesome/free-solid-svg-icons';
import styled from 'styled-components/macro';
import { Link } from 'react-router-dom';
import FlashMessageRender from '@/components/FlashMessageRender';
import MessageBox from '@/components/MessageBox';

import getKnowledgebaseList from '@/api/knowledgebase/getKnowledgebaseList';

const Code = styled.code`${tw`font-mono py-1 px-2 bg-neutral-900 rounded text-sm inline-block`}`;

export interface KnowledgebaseListResponse {
    questions: any[];
    categories: any[];
    settings: any[];
}


type Props = {
    id: string;
}

export default ({ match }: RouteComponentProps<Props>) => {

    var id = match.params.id;

    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error, mutate } = useSWR<KnowledgebaseListResponse>([ id, '/knowledgebase' ], (id) => getKnowledgebaseList(id));

    useEffect(() => {
        if (!error) {
            clearFlashes('knowledgebase');
        } else {
            clearAndAddHttpError({ key: 'knowledgebase', error });
        }
    });

    return (
        <PageContentBlock title={'Knowledgebase List'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'knowledgebase'} css={tw`mb-4`} />
            </div>
            {!data ?
                <div css={tw`w-full`}>
                    <Spinner size={'large'} centered />
                </div>
                :
                <>
                    <div css={tw`w-full`}>
                        {data.questions.length < 1 ?
                            <MessageBox type="info" title="Info">
                                There are no questions.
                            </MessageBox>
                            :
                            (data.questions.map((item, key) => (
                                <GreyRowBox as={Link} to={`/knowledgebase/page/${item.id}`} css={tw`mb-2`} key={key}>
                                    <div css={tw`hidden md:block`}>
                                        <FontAwesomeIcon icon={faBook} fixedWidth/>
                                    </div>
                                    <div css={tw`ml-8 text-center`}>
                                        <p css={tw`text-sm`}><Code>#{item.id}</Code></p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>ID</p>
                                    </div>
                                    <div css={tw`ml-8 text-center hidden md:block`}>
                                        <p css={tw`text-sm`}>{item.author}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Author</p>
                                    </div>
                                    <div css={tw`ml-8 text-center`}>
                                        <p css={tw`text-sm`}>{item.subject}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Subject</p>
                                    </div>
                                    <div css={tw`ml-8 text-center`}>
                                        <p css={tw`text-sm`}>{item.categoryname.name}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Category</p>
                                    </div>
                                    <div css={tw`flex-1 ml-4 text-center hidden md:block`}>
                                        <p css={tw`text-sm`}><span dangerouslySetInnerHTML={{ __html: item.information.substr(0, 40) + (item.information.length > 40 ? '...' : '') }}></span></p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Information</p>
                                    </div>
                                    <div css={tw`ml-8 text-center hidden md:block`}>
                                        <p css={tw`text-sm`}>{item.updated_at}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Updated</p>
                                    </div>
                                </GreyRowBox>
                            )))
                        }
                    </div>
                </>
            }
        </PageContentBlock>
    );
};
